<?php

namespace App\AnotherClasses\TopUp;


class TopUpRequest extends RequestHelper
{
    public function __construct($type, $terminal_id, $password)
    {
        parent::__construct(TopupConstants::REQUEST_TEMPLATE);

        $this->setValue("request_type", $type);
        $this->setValue("terminal_id", $terminal_id);
        $this->setValue("password", $password);
    }

    public function getResponse()
    {
        $curl = curl_init();

        $headers = array();
        $headers[] = 'Accept: application/xml';
        $headers[] = 'Content-Type: application/xml';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.qiwi.com/xml/topup.jsp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => strval($this)
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public function requestBody($value)
    {
        $this->setValue("request_body", $value);
    }
}