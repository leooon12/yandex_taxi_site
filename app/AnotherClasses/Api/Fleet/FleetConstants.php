<?php

namespace App\AnotherClasses\Api\Fleet;

/**
 * Class FleetConstants Содержит константы, используемые в запросах к Fleet-API
 * @package App\AnotherClasses\Api\Fleet
 */
class FleetConstants
{
    /**
     * Основной адрес API
     */
    const API_URL = "https://fleet-api.taxi.yandex.net/";

    /**
     * Адрес функции DriverProfiles относительно основного адреса API
     */
    const DRIVER_PROFILES_RELATIVE_URL = "v1/parks/driver-profiles/list";
}