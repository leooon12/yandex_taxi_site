<?php


namespace App\Http\Controllers;

use App\AnotherClasses\TopUpRequestManager;

class TopUpController extends Controller
{
    //Очевидно, что так быть не должно, но я не знаю, где мне это хранить =(
    private $_terminal_id = "1234567890";

    //Так тем более не надо делать
    private $_password = "0987654321";

    public function makePayment($account_number, $amount)
    {
        $request_manager = new TopUpRequestManager($this->_terminal_id, $this->_password);
        $response = $this->parseResponse($request_manager->makePayment($account_number, $amount));

        return response()->json($response);
    }

    public function checkPayment($account_number, $transaction_number)
    {
        $request_manager = new TopUpRequestManager($this->_terminal_id, $this->_password);
        $response = $this->parseResponse($request_manager->checkPayment($account_number, $transaction_number));

        return response()->json($response);
    }

    private function parseResponse($response)
    {
        $result = array();
        $parsed_xml = simplexml_load_string($response);

        if ((string) $parsed_xml->{'result-code'} != "0")
        {
            $result['status'] = (string) $parsed_xml->{'result-code'};
        }
        else
        {
            $result['status'] = 0;

            $result['payment'] = array(
                'status' => (string) $parsed_xml->payment->attributes()['status'],
                'transaction_number' => (string) $parsed_xml->payment->attributes()['transaction-number']
            );
        }

        return $result;
    }
}