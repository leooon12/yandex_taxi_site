<?php

namespace App\AnotherClasses\Api\Fleet;

/**
 * Содержит константы, используемые в запросах к Fleet-API
 * @package App\AnotherClasses\Api\Fleet
 */
class FleetConstants
{
    /**
     * Основной адрес API
     */
    const API_URL = 'https://fleet-api.taxi.yandex.net/';

    /**
     * Адрес функции DriverProfiles относительно основного адреса API
     */
    const DRIVER_PROFILES_URL = FleetConstants::API_URL.'v1/parks/driver-profiles/list';

    /**
     * Адрес функции Transactions относительно основного адреса API
     */
    const CREATE_TRANSACTION_URL = FleetConstants::API_URL.'v2/parks/driver-profiles/transactions';

    /**
     * Идентификатор ручного типа операции
     */
    const PARTNER_SERVICE_MANUAL_CATEGORY_ID = 'partner_service_manual';
}