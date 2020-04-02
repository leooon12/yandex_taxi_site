<?php

namespace App\AnotherClasses\Api\TopUp;

use App\AnotherClasses\Api\ApiPostRequest;

class TopUpRequest extends TopUpRequestBuilder
{
    use ApiPostRequest;

    public function __construct($type, $terminal_id, $password)
    {
        parent::__construct(TopUpConstants::REQUEST_TEMPLATE);

        $this->setValue("request_type", $type);
        $this->setValue("terminal_id", $terminal_id);
        $this->setValue("password", $password);
    }

    public function getResponse()
    {
        $headers = array();
        $headers[] = 'Accept: application/xml';
        $headers[] = 'Content-Type: application/xml';

        return $this->getResponseBase('https://api.qiwi.com/xml/topup.jsp', $headers, $this->toString());
    }

    public function requestBody($value)
    {
        $this->setValue("request_body", $value);
    }
}