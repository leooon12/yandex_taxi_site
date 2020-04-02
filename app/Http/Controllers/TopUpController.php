<?php


namespace App\Http\Controllers;

use App\AnotherClasses\TopUpRequestManager;
use Illuminate\Support\Facades\Config;

class TopUpController extends Controller
{
    const REQUEST_RESULT_SUCCESS = 0;

    const PAYMENT_RESULT_MIN_POSSIBLE_VALUE = 50;

    const PAYMENT_RESULT_MAX_POSSIBLE_VALUE = 60;

    const PAYMENT_RESULT_SUCCESS = 60;

    const PAYMENT_RESULT_FAILED = 160;

    public static function makePayment($account_number, $amount)
    {
        $request_manager = new TopUpRequestManager(Config::get('topup.terminal_id'), Config::get('topup.password'));
        $topup_response = $request_manager->makePayment($account_number, $amount);

        return self::parseResponse($topup_response);
    }

    public static function checkPayment($account_number, $transaction_number)
    {
        $request_manager = new TopUpRequestManager(Config::get('topup.terminal_id'), Config::get('topup.password'));
        $topup_response = $request_manager->checkPayment($account_number, $transaction_number);

        return self::parseResponse($topup_response);
    }

    public static function checkBalance()
    {
        $request_manager = new TopUpRequestManager(Config::get('topup.terminal_id'), Config::get('topup.password'));
        $response = $request_manager->checkBalance();

        return $response;
    }

    private static function parseResponse($response)
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
