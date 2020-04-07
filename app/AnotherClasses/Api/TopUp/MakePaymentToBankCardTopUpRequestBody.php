<?php

namespace App\AnotherClasses\Api\TopUp;

class MakePaymentToBankCardTopUpRequestBody extends TopUpRequestBuilder
{
    public function __construct($card_number, $amount, $currency = "RUB")
    {
        parent::__construct(TopUpConstants::MAKE_PAYMENT_TO_BANK_CARD_BODY_TEMPLATE);

        $this->setValue("account_number", $card_number);
        $this->setValue("amount", $amount);
        $this->setValue("currency", $currency);
        $this->setValue("transaction_number", $this->generateTransactionNumber());
    }

    private function generateTransactionNumber()
    {
        return rand(1, getrandmax());
    }
}