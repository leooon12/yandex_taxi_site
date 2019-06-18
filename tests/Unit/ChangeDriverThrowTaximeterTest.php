<?php

namespace Tests\Unit;

use App\AnotherClasses\Builders\FullDriverInfo;
use App\AnotherClasses\TaximeterConnector;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeDriverThrowTaximeterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $phone = "9143301234";

        $profile = TaximeterConnector::getDriverProfile($phone);
        $versionAndImei = TaximeterConnector::getAdditionalDriverInfo($phone);

        $driverInfo = new FullDriverInfo();
        $driverInfo->setAllFromTaximeterDriverProfile($profile);
        $driverInfo->setImei($versionAndImei[1]);
        $driverInfo->setTaximeterVesion($versionAndImei[0]);

        $driverInfo->setPhone("89143301233");

        //TaximeterConnector::editDriver($driverInfo);
        $this->assertTrue(true);
    }
}
