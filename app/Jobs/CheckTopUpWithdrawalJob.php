<?php

namespace App\Jobs;

use App\AnotherClasses\Payments\PaymentRequest;
use App\AnotherClasses\Api\WithdrawalUtils;
use App\Http\Controllers\TopUpController;
use App\TopUpWithdrawal;
use App\WithdrawalStatus;
use BadMethodCallException;
use http\Exception\UnexpectedValueException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Выполняет проверку выплаты
 *
 * @package App\Jobs
 */
class CheckTopUpWithdrawalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Время, через которое необходимо выполнить задачу
     */
    const DELAY_TIME_IN_MINUTES = 10;

    /**
     * @var PaymentRequest Запрос на выплату
     */
    private $payment_request;

    /**
     * @var TopUpWithdrawal Выплата
     */
    private $top_up_withdrawal;

    /**
     * Создает новый экземпляр класса
     *
     * @param $top_up_withdrawal TopUpWithdrawal Выплата
     * @param $payment_request PaymentRequest Запрос на выплату
     */
    public function __construct($top_up_withdrawal, $payment_request)
    {
        $this->top_up_withdrawal = $top_up_withdrawal;
        $this->payment_request = $payment_request;
    }

    /**
     * Выполняет работу (проверку выплаты)
     *
     * @throws UnexpectedValueException
     *
     * @return void
     */
    public function handle()
    {
        //Поиск водителя, запросившего выплату
        $driver_profile = WithdrawalUtils::getDriverProfile($this->payment_request->getUserId());

        if ($driver_profile == null)
        {
            $this->rollbackPayment(TopUpController::PAYMENT_RESULT_FAILED, $driver_profile);
            throw new UnexpectedValueException('Не удалось найти водителя с идентификатором пользователя '.$this->payment_request->getUserId());
        }

        //Обращение к TopUp API для получения статуса платежа
        $top_up_response = TopUpController::checkPayment($this->top_up_withdrawal->requisites, $this->top_up_withdrawal->transaction_number);

        //Ошибка ПРОВЕРКИ платежа. Не знаю, как это может произойти, но, подозреваю, что это стоит предусмотреть
        if ($top_up_response['status'] != TopUpController::REQUEST_RESULT_SUCCESS)
        {
            $this->rollbackPayment(TopUpController::PAYMENT_RESULT_FAILED, null);
            return;
        }

        //Ошибка проведения платежа
        //TODO: Необходимо подумать на тему отдельной проверки статусов с номерами 40-49 (по описанию API это означает, что необходимо обратиться в поддержку)
        if ($top_up_response['payment']['status'] < TopUpController::PAYMENT_RESULT_MIN_POSSIBLE_VALUE ||
            $top_up_response['payment']['status'] > TopUpController::PAYMENT_RESULT_MAX_POSSIBLE_VALUE)
        {
            $this->rollbackPayment($top_up_response['payment']['status'], $driver_profile);
            return;
        }

        //Платеж проведен успешно
        if ($top_up_response['payment']['status'] == TopUpController::PAYMENT_RESULT_SUCCESS)
        {
            //Установка статуса запроса платежа "Выполнен" и установка статуса платежа из запроса
            $this->payment_request->setStatus(WithdrawalStatus::COMPLETED);
            $this->top_up_withdrawal->update(['status' => $top_up_response['payment']['status']]);

            return;
        }

        //Платеж все еще в обработке, установка статуса платежа из запроса и повтор через десять минут
        $this->top_up_withdrawal->update(['status' => $top_up_response['payment']['status']]);
        CheckTopUpWithdrawalJob::dispatch($this->top_up_withdrawal, $this->payment_request)->delay(now()->addMinutes(CheckTopUpWithdrawalJob::DELAY_TIME_IN_MINUTES));
    }

    /**
     * Осуществляет откат платежа
     *
     * @param mixed $top_up_withdrawal_status Статус выплаты после отката
     * @param mixed $driver_profile Профиль водителя
     */
    private function rollbackPayment($top_up_withdrawal_status, $driver_profile = null)
    {
        $this->top_up_withdrawal->update(['status' => $top_up_withdrawal_status]);
        $this->payment_request->setStatus(WithdrawalStatus::CANCELED);

        if ($driver_profile != null)
        {
            if (!WithdrawalUtils::addToBalance($driver_profile, $this->payment_request->getSum()))
            {
                throw new BadMethodCallException('Произошла ошибка проведения автовыплаты, не удалось вернуть '.$this->payment_request.' рублей водителю с идентификатором "'.$driver_profile['driver_profile']['id'].'"');
            }
        }
    }
}
