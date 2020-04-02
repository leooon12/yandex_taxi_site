<?php

namespace App\Jobs;

use App\AnotherClasses\Api\WithdrawalUtils;
use App\Http\Controllers\TopUpController;
use App\TopUpWithdrawal;
use App\WithdrawalBankCard;
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
     * @var integer Идентификатор запроса платежа в базе данных
     */
    private $top_up_withdrawal_id;

    /**
     * Создает новый экземпляр класса
     *
     * @param integer Идентификатор запроса платежа в базе данных
     *
     * @return void
     */
    public function __construct($top_up_withdrawal_id)
    {
        $this->top_up_withdrawal_id = $top_up_withdrawal_id;
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
        //Получение выплаты из базы данных
        $top_up_withdrawal = TopUpWithdrawal::find($this->top_up_withdrawal_id);

        if ($top_up_withdrawal == null)
        {
            $this->rollbackPayment($top_up_withdrawal, TopUpController::PAYMENT_RESULT_FAILED, $top_up_withdrawal);
            throw new UnexpectedValueException('Не удалось найти выплату с идентификатором '.$this->top_up_withdrawal_id);
        }

        //Получение запроса на выплату из базы данных
        $withdrawal_bank_card = WithdrawalBankCard::find($top_up_withdrawal->withdrawal_bank_card_id);

        if ($withdrawal_bank_card == null)
        {
            $this->rollbackPayment($top_up_withdrawal, TopUpController::PAYMENT_RESULT_FAILED, $top_up_withdrawal);
            throw new UnexpectedValueException('Не удалось найти запрос на выплату с идентификатором '.$top_up_withdrawal->withdrawal_bank_card_id);
        }

        //Поиск водителя, запросившего выплату
        $driver_profile = WithdrawalUtils::getDriverProfile($withdrawal_bank_card->user_id);

        if ($driver_profile == null)
        {
            $this->rollbackPayment($top_up_withdrawal, TopUpController::PAYMENT_RESULT_FAILED, $top_up_withdrawal);
            throw new UnexpectedValueException('Не удалось найти водителя с идентификатором пользователя '.$withdrawal_bank_card->user_id);
        }

        //Обращение к TopUp API для получения статуса платежа
        $top_up_response = TopUpController::checkPayment($top_up_withdrawal->card_number, $top_up_withdrawal->transaction_number);

        //Ошибка ПРОВЕРКИ платежа. Не знаю, как это может произойти, но, подозреваю, что это стоит предусмотреть
        if ($top_up_response['status'] != TopUpController::REQUEST_RESULT_SUCCESS)
        {
            $this->rollbackPayment($top_up_withdrawal, TopUpController::PAYMENT_RESULT_FAILED, $withdrawal_bank_card, $driver_profile);
            return;
        }

        //Ошибка проведения платежа
        //TODO: Необходимо подумать на тему отдельной проверки статусов с номерами 40-49 (по описанию API это означает, что необходимо обратиться в поддержку)
        if ($top_up_response['payment']['status'] < TopUpController::PAYMENT_RESULT_MIN_POSSIBLE_VALUE ||
            $top_up_response['payment']['status'] > TopUpController::PAYMENT_RESULT_MAX_POSSIBLE_VALUE)
        {
            $this->rollbackPayment($top_up_withdrawal, $top_up_response['payment']['status'], $withdrawal_bank_card, $driver_profile);
            return;
        }

        //Платеж проведен успешно
        if ($top_up_response['payment']['status'] == TopUpController::PAYMENT_RESULT_SUCCESS)
        {
            //Установка статуса запроса платежа "Выполнен" и установка статуса платежа из запроса
            $withdrawal_bank_card->update(['status_id' => WithdrawalStatus::COMPLETED]);
            $top_up_withdrawal->update(['status' => $top_up_response['payment']['status']]);

            return;
        }

        //Платеж все еще в обработке, установка статуса платежа из запроса и повтор через десять минут
        $top_up_withdrawal->update(['status' => $top_up_response['payment']['status']]);
        CheckTopUpWithdrawalJob::dispatch($this->top_up_withdrawal_id)->delay(now()->addMinutes(CheckTopUpWithdrawalJob::DELAY_TIME_IN_MINUTES));
    }

    /**
     * Осуществляет откат платежа
     *
     * @param mixed $top_up_withdrawal Выплата
     * @param mixed $top_up_withdrawal_status Статус выплаты после отката
     * @param mixed $withdrawal Запрос на выплату
     * @param mixed $driver_profile Профиль водителя
     */
    private function rollbackPayment($top_up_withdrawal, $top_up_withdrawal_status, $withdrawal, $driver_profile = null)
    {
        if ($top_up_withdrawal != null)
        {
            $top_up_withdrawal->update(['status' => $top_up_withdrawal_status]);
        }

        if ($withdrawal != null)
        {
            $withdrawal->update(['status_id' => WithdrawalStatus::CANCELED]);

            if ($driver_profile != null)
            {
                if (!WithdrawalUtils::addToBalance($driver_profile, $withdrawal->sum))
                {
                    throw new BadMethodCallException('Произошла ошибка проведения автовыплаты, не удалось вернуть '.$withdrawal->sum.' рублей водителю с идентификатором "'.$driver_profile['driver_profile']['id'].'"');
                }
            }
        }
    }
}