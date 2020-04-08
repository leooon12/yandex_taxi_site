<?php

namespace App\AnotherClasses\Payments;

use App\Http\Controllers\TopUpController;
use App\TopUpWithdrawal;
use App\WithdrawalQiwi;

/**
 * Выплата на Qiwi-кошелек
 * @package App\AnotherClasses\Payments
 */
class QiwiWalletPaymentRequest extends PaymentRequest
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
        return '7'.$this->getPhoneNumberWithoutCountryCode($this->withdrawal->qiwi_number);
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return TopUpWithdrawal::QIWI_WITHDRAWAL_TYPE;
    }

    /**
     * @inheritDoc
     */
    public function makePayment($sum)
    {
        return TopUpController::makePaymentToQiwiWallet($this->getRequisites(), $sum);
    }

    /**
     * Преобразует номер телефона в любом формате в номер телефона без кода страны
     *
     * @param $phone_number string Номер телефона
     *
     * @return string Номер телефона без кода страны
     */
/*    private function getPhoneNumberWithoutCountryCode($phone_number)
    {
        $matches = array();
        preg_match("/(\+7|8|)([0-9]+)/", $phone_number, $matches);

        return $matches[2];
    }*/
    private function getPhoneNumberWithoutCountryCode($phone_number)
    {
        $matches = array();
        preg_match("/(\+7|8|)([0-9]+)/", preg_replace("/[\(\)\s-]+/", '', $phone_number), $matches);

        return $matches[2];
    }
}