<?php

namespace App\AnotherClasses\Api;

use App\Http\Controllers\FleetController;
use TCG\Voyager\Models\User;

/**
 * Содержит вспомогательные функции для работы с выплатами
 * @package App\AnotherClasses\Api
 */
class WithdrawalUtils
{
    /**
     * Находит профиль водителя по номеру телефона
     *
     * @param int $user_id Идентификатор пользователя в базе данных
     *
     * @return array|null Профиль водителя
     */
    public static function getDriverProfile($user_id)
    {
        //Находим пользователя, который запросил выплату
        $user = User::find($user_id);

        if ($user == null)
        {
            return null;
        }

        //Находим профиль водителя по номеру телефона
        $driver_profiles = FleetController::getDriverProfiles($user->phone_number);

        //Если запрос выполнен с ошибкой или найдено несколько записей, то продолжить невозможно
        if ($driver_profiles['status']['code'] != 200 || $driver_profiles['total'] != 1)
        {
            return null;
        }

        return $driver_profiles['driver_profiles'][0];
    }

    /**
     * Проверяет, достаточно ли средств на балансе водителя
     *
     * @param array $driver_profile Профиль водителя
     * @param double $amount Сумма к выплате
     *
     * $@return bool
     */
    public static function isDriverBalanceSufficient($driver_profile, $amount)
    {
        return $driver_profile['accounts'][0]['balance'] >= $amount;
    }

    /**
     * Списывает деньги с водительского профиля
     *
     * @param array $driver_profile Профиль водителя
     * @param double $amount Сумма списания
     *
     * @return bool
     */
    public static function takeFromBalance($driver_profile, $amount)
    {
        $response = FleetController::createTransaction(-$amount, $driver_profile['driver_profile']['id']);
        return $response['status']['code'] == 200;
    }

    /**
     * Добавляет деньги на баланс водителя
     *
     * @param array $driver_profile Профиль водителя
     * @param double $amount Сумма добавления
     *
     * @return bool
     */
    public static function addToBalance($driver_profile, $amount)
    {
        $response = FleetController::createTransaction($amount, $driver_profile['driver_profile']['id']);
        return $response['status']['code'] == 200;
    }
}
