<?php


namespace App\AnotherClasses\Api\TopUp;


class MakePaymentToQiwiWalletTopUpRequestBody extends TopUpRequestBuilder
{
    public function __construct($phone_number, $amount, $currency = "RUB")
    {
        parent::__construct(TopUpConstants::MAKE_PAYMENT_TO_QIWI_WALLET_BODY_TEMPLATE);

        $this->setValue("account_number", $phone_number);
        $this->setValue("amount", $amount);
        $this->setValue("currency", $currency);
        $this->setValue("transaction_number", $this->generateTransactionNumber());
    }

    private function generateTransactionNumber()
    {
        return rand(1, getrandmax());
    }
}