<?php

namespace App\AnotherClasses\TopUp;

class MakePaymentRequestBody extends RequestHelper
{
    public function __construct($account_number, $amount, $currency = "RUB")
    {
        parent::__construct(TopUpConstants::MAKE_PAYMENT_BODY_TEMPLATE);

        $this->setValue("account_number", $account_number);
        $this->setValue("amount", $amount);
        $this->setValue("currency", $currency);
        $this->setValue("transaction_number", $this->generateTransactionNumber());
    }

    private function generateTransactionNumber()
    {
        return rand(1, getrandmax());
    }
}