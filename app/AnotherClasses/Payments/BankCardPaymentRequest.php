<?php

namespace App\AnotherClasses\Payments;

use App\Http\Controllers\TopUpController;
use App\TopUpWithdrawal;
use App\WithdrawalQiwi;

/**
 * Выплата на банковскую карту
 * @package App\AnotherClasses\Payments
 */
class BankCardPaymentRequest extends PaymentRequest
{
    /**
     * Конструктор
     *
     * @param $withdrawal WithdrawalBankCard|WithdrawalQiwi Запрос на выплату
     */
    public function __construct($withdrawal)
    {
        parent::__construct($withdrawal);
    }

    /**
     * @inheritDoc
     */
    public function getRequisites()
    {
        return $this->withdrawal->card_number;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return TopUpWithdrawal::BANK_CARD_WITHDRAWAL_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function makePayment($sum)
    {
        return TopUpController::makePaymentToBankCard($this->getRequisites(), $sum);
    }
}