<?php

namespace App\AnotherClasses\TopUp;

class CheckUserRequestBody extends RequestHelper
{
    public function __construct($phone_number, $currency = "RUB")
    {
        parent::__construct(TopUpConstants::CHECK_USER_REQUEST_BODY_TEMPLATE);

        $this->setValue("phone_number", $phone_number);
        $this->setValue("currency", $currency);
    }
}
