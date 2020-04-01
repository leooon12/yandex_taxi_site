<?php

namespace App\Http\Controllers;

use App\AnotherClasses\Api\Fleet\DriverProfilesFleetRequestBody;
use App\AnotherClasses\Api\Fleet\FleetConstants;
use App\AnotherClasses\Api\Fleet\FleetRequest;
use Illuminate\Support\Facades\Config;

class TaximeterController extends Controller
{
    public function getDriverProfiles($filter_string = '')
    {
        $fleet_request = new FleetRequest(FleetConstants::API_URL.FleetConstants::DRIVER_PROFILES_RELATIVE_URL, Config::get('fleet.api_key'), Config::get('fleet.client_id'));
        $fleet_request->requestBody((new DriverProfilesFleetRequestBody(Config::get('fleet.park_id'), $filter_string))->toString());

        return print_r(json_decode($fleet_request->getResponse(), true), true);
    }

    public function getDriverProfilesWithoutFilter()
    {
        return $this->getDriverProfiles();
    }
}