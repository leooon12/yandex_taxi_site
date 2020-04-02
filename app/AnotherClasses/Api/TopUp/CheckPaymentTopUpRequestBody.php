<?php

namespace App\AnotherClasses\Api\TopUp;

class CheckPaymentTopUpRequestBody extends TopUpRequestBuilder
{
    public function __construct($account_number, $transaction_number)
    {
        parent::__construct(TopUpConstants::CHECK_PAYMENT_REQUEST_BODY_TEMPLATE);

        $this->setValue("account_number", $account_number);
        $this->setValue("transaction_number", $transaction_number);
    }
}