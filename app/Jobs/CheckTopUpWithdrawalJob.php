<?php

namespace App\Jobs;

use App\Http\Controllers\TopUpController;
use App\TopUpWithdrawal;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use http\Exception\UnexpectedValueException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TCG\Voyager\Models\User;

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
        //Получение необходимых данных из базы данных
        $top_up_withdrawal = $this->getTopUpWithdrawal();
        $withdrawal_bank_card = $this->getWithdrawalBankCard($top_up_withdrawal->withdrawal_bank_card_id);
        $user = $this->getUser($withdrawal_bank_card->user_id);

        //Обращение к TopUp API для получения статуса платежа
        $top_up_response = TopUpController::checkPayment($top_up_withdrawal->card_number, $top_up_withdrawal->transaction_number);

        //Ошибка ПРОВЕРКИ платежа. Не знаю, как это может произойти, но, подозреваю, что это стоит предусмотреть
        if ($top_up_response['status'] != TopUpController::REQUEST_RESULT_SUCCESS)
        {
            //Установка статуса запроса платежа "Отклонен" и ручная установка статуса платежа
            $withdrawal_bank_card->update(['status_id' => WithdrawalStatus::CANCELED]);
            $top_up_withdrawal->update(['status' => TopUpController::PAYMENT_RESULT_FAILED]);

            //Отката выплаты в яндексе
            //TODO

            return;
        }

        //Ошибка проведения платежа
        //TODO: Необходимо подумать на тему отдельной проверки статусов с номерами 40-49 (по описанию API это означает, что необходимо обратиться в поддержку)
        if ($top_up_response['payment']['status'] < TopUpController::PAYMENT_RESULT_MIN_POSSIBLE_VALUE ||
            $top_up_response['payment']['status'] > TopUpController::PAYMENT_RESULT_MAX_POSSIBLE_VALUE)
        {
            //Установка статуса запроса платежа "Отклонен" и установка статуса платежа из запроса
            $withdrawal_bank_card->update(['status_id' => WithdrawalStatus::CANCELED]);
            $top_up_withdrawal->update(['status' => $top_up_response['payment']['status']]);

            //Отката выплаты в яндексе
            //TODO

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
     * Находит выплату с top_up_withdrawal_id, указанным в констурукторе
     *
     * @throws UnexpectedValueException не удалось найти запись с указанным id в базе данных
     *
     * @return TopUpWithdrawalValue
     */
    private function getTopUpWithdrawal()
    {
        $top_up_withdrawal = TopUpWithdrawal::find($this->top_up_withdrawal_id);

        if ($top_up_withdrawal == null)
        {
            throw new UnexpectedValueException('Не удалось получить запись из TopUpWithdrawal с id = '.$this->top_up_withdrawal_id);
        }

        return $top_up_withdrawal;
    }

    /**
     * Находит запрос на выплату
     *
     * @param $withdrawal_bank_card_id integer Идентификатор запроса на выплату в базе данных
     *
     * @throws UnexpectedValueException не удалось найти запись с указанным id в базе данных
     *
     * @return WithdrawalBankCardValue
     */
    private function getWithdrawalBankCard($withdrawal_bank_card_id)
    {
        $withdrawal_bank_card = WithdrawalBankCard::find($withdrawal_bank_card_id);

        if ($withdrawal_bank_card == null)
        {
            throw new UnexpectedValueException('Не удалось получить запись из WithdrawalBankCard с id = '.$withdrawal_bank_card_id);
        }

        return $withdrawal_bank_card;
    }

    /**
     * Находит пользователя, совершившего запрос на выплату
     *
     * @param $user_id integer Идентификатор пользователя в базе данных
     *
     * @throws UnexpectedValueException не удалось найти запись с указанным id в базе данных
     *
     * @return UserValue
     */
    private function getUser($user_id)
    {
        $user = User::find($user_id);

        if ($user == null)
        {
            throw new UnexpectedValueException('Не удалось получить запись из User с id = '.$this->user_id);
        }

        return $user;
    }


}