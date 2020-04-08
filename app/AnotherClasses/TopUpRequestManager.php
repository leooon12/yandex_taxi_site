<?php

namespace App\AnotherClasses;

use App\AnotherClasses\Api\TopUp\CheckPaymentTopUpRequestBody;
use App\AnotherClasses\Api\TopUp\CheckUserTopUpRequestBody;
use App\AnotherClasses\Api\TopUp\MakePaymentToBankCardTopUpRequestBody;
use App\AnotherClasses\Api\TopUp\MakePaymentToQiwiWalletTopUpRequestBody;
use App\AnotherClasses\Api\TopUp\TopUpRequest;

class TopUpRequestManager
{
    private $_terminal_id;

    private $_password;

    public function __construct($terminal_id, $password)
    {
        $this->_terminal_id = $terminal_id;
        $this->_password = $password;
    }

    public function getBalance()
    {
        $request = new TopUpRequest("ping", $this->_terminal_id, $this->_password);
        $request->requestBody("");

        return $request->getResponse();
    }

    public function checkPayment($account_number, $transaction_number)
    {
        $request = new TopUpRequest("pay", $this->_terminal_id, $this->_password);
        $request->requestBody((new CheckPaymentTopUpRequestBody($account_number, $transaction_number))->toString());

        return $request->getResponse();
    }

    public function checkUser($phone_number)
    {
        $request = new TopUpRequest("check-user", $this->_terminal_id, $this->_password);
        $request->requestBody((new CheckUserTopUpRequestBody($phone_number))->toString());

        return $request->getResponse();
    }

    public function makePaymentToBankCard($card_number, $amount)
    {
        $request = new TopUpRequest("pay", $this->_terminal_id, $this->_password);
        $request->requestBody((new MakePaymentToBankCardTopUpRequestBody($card_number, $amount))->toString());

        return $request->getResponse();
    }

    public function makePaymentToQiwiWallet($phone_number, $amount)
    {
        $request = new TopUpRequest("pay", $this->_terminal_id, $this->_password);
        $request->requestBody((new MakePaymentToQiwiWalletTopUpRequestBody($phone_number, $amount))->toString());

        return $request->getResponse();
    }
}