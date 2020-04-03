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

    public static function getBalance()
    {
        $request_manager = new TopUpRequestManager(Config::get('topup.terminal_id'), Config::get('topup.password'));
        $parsed_response = simplexml_load_string($request_manager->checkBalance());

        $result = array();

        if ((string) $parsed_response->{'result-code'} != "0")
        {
            $result['status'] = (string) $parsed_response->{'result-code'};
        }
        else
        {
            $result['status'] = 0;
            $result['balances'] = array();
            $balances_counter = 0;

            foreach ($parsed_response->balances->children() as $balance)
            {
                $result['balances'][$balances_counter] = array
                (
                    'currency_code' => strval($balance->attributes()[0]),
                    'balance' => strval($balance)
                );
            }
        }

        return print_r($result, true);
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
