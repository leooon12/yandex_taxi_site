<?php

namespace App\AnotherClasses\Api\TopUp;

class CheckUserTopUpRequestBody extends TopUpRequestBuilder
{
    public function __construct($phone_number, $currency = "RUB")
    {
        parent::__construct(TopUpConstants::CHECK_USER_REQUEST_BODY_TEMPLATE);

        $this->setValue("phone_number", $phone_number);
        $this->setValue("currency", $currency);
    }
}
