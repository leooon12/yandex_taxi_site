<?php

namespace App\Http\Controllers;

use App\AnotherClasses\Api\Fleet\CreateTransactionFleetRequestBody;
use App\AnotherClasses\Api\Fleet\DriverProfilesFleetRequestBody;
use App\AnotherClasses\Api\Fleet\FleetConstants;
use App\AnotherClasses\Api\Fleet\FleetRequest;
use Illuminate\Support\Facades\Config;

/**
 * Class FleetController Контролер для обращения к Fleet-API
 * @package App\Http\Controllers
 */
class FleetController extends Controller
{
    /**
     * Получает профили водителей, зарегестрированных в системе
     *
     * @param string $filter_string Фильтрующая строка (будут возвращены только те профили, в полях которых присутствует эта строка)
     *
     * @return array Результат выполнения запроса с полями:
     *      status => code (результат работы, 200 - успех, остальное - ошибка), message (описание ошибки)
     *      total => число возвращенных записей
     *      driver_profiles => полученные профили водителей
     */
    public static function getDriverProfiles($filter_string = '')
    {
        $fleet_request = new FleetRequest(FleetConstants::DRIVER_PROFILES_URL, Config::get('fleet.api_key'), Config::get('fleet.client_id'));
        $fleet_request->requestBody((new DriverProfilesFleetRequestBody(Config::get('fleet.park_id'), $filter_string))->toString());

        return self::parseResponse($fleet_request->getResponse(), 'parseGetDriverProfilesResponse');
    }

    /**
     * Создает транзакцию (перевод или списание денежных средств со счета водителя)
     *
     * @param double $amount Сумма списания
     * @param string $driver_id Идентификатор водителя
     *
     * @return array Результат выполнения запроса
     *      status => code (результат работы, 200 - успех, остальное - ошибка), message (описание ошибки)
     *      transaction => описание созданной транзакции
     */
    public static function createTransaction($amount, $driver_id)
    {
        $fleet_request = new FleetRequest(FleetConstants::CREATE_TRANSACTION_URL, Config::get('fleet.api_key'), Config::get('fleet.client_id'));
        $fleet_request->requestBody((new CreateTransactionFleetRequestBody($amount, FleetConstants::PARTNER_SERVICE_MANUAL_CATEGORY_ID, 'Auto payment', $driver_id, Config::get('fleet.park_id')))->toString());

        return self::parseResponse($fleet_request->getResponse(true), 'parseCreateTransaction');
    }

    /**
     * Выполняет обработку полученного ответа
     *
     * @param string $response Полученный JSON-ответ
     * @param string $callback Имя функции обратного вызова, которая выполняет обработку ответа
     *
     * @return array Обработанный ответ
     */
    private static function parseResponse($response, $callback)
    {
        $parsed_response = json_decode($response, true);

        $result = array
        (
            'status' => array(),
        );

        if (!array_key_exists('code', $parsed_response))
        {
            $result['status']['code'] = 200;
            $result['status']['message'] = 'Success';
        }
        else
        {
            $result['status']['code'] = $parsed_response['code'];
            $result['status']['code'] = $parsed_response['message'];
        }

        return call_user_func(array('App\Http\Controllers\FleetController', $callback), $result, $parsed_response);
    }

    /**
     * Функция обратного вызова для обработки ответа от GetDriverProfiles
     *
     * @param array $result Ответ с установленным статусом
     * @param array $parsed_response Ответ в виде ассоциативного массива
     *
     * @return array Обработанный ответ
     */
    private static function parseGetDriverProfilesResponse($result, $parsed_response)
    {
        if (array_key_exists('total', $parsed_response))
        {
            $result['total'] = $parsed_response['total'];
            $result['driver_profiles'] = $parsed_response['driver_profiles'];
        }

        return $result;
    }

    /**
     * Функция обратного вызова для обработки ответа от CreateTransaction
     *
     * @param array $result Ответ с установленным статусом
     * @param array $parsed_response Ответ в виде ассоциативного массива
     *
     * @return array Обработанный ответ
     */
    private static function parseCreateTransaction($result, $parsed_response)
    {
        if ($result['status']['code'] == 200)
        {
            $result['transaction'] = $parsed_response;
        }

        return $result;
    }
}