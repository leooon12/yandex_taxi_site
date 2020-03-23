<?php

namespace App\AnotherClasses;

use App\AnotherClasses\TopUp\TopUpRequest;
use App\AnotherClasses\TopUp\CheckUserRequestBody;
use App\AnotherClasses\TopUp\CheckPaymentRequestBody;
use App\AnotherClasses\TopUp\MakePaymentRequestBody;

class TopUpRequestManager
{
    private $_terminal_id;

    private $_password;

    public function __construct($terminal_id, $password)
    {
        $this->_terminal_id = $terminal_id;
        $this->_password = $password;
    }

    public function checkBalance()
    {
        $request = new TopUpRequest("ping", $this->_terminal_id, $this->_password);
        $request->requestBody("");

        return $request->getResponse();
    }

    public function checkPayment($account_number, $transaction_number)
    {
        $request = new TopUpRequest("pay", $this->_terminal_id, $this->_password);
        $request->requestBody(new CheckPaymentRequestBody($account_number, $transaction_number));

        return $request->getResponse();
    }

    public function checkUser($phone_number)
    {
        $request = new TopUpRequest("check-user", $this->_terminal_id, $this->_password);
        $request->requestBody(new CheckUserRequestBody($phone_number));

        return $request->getResponse();
    }

    public function makePayment($account_number, $amount)
    {
        $request = new TopUpRequest("pay", $this->_terminal_id, $this->_password);
        $request->requestBody(new MakePaymentRequestBody($account_number, $amount));

        return $request->getResponse();
    }
}