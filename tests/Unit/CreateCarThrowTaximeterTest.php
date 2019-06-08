<?php

namespace Tests\Unit;

use App\AnotherClasses\Builders\CarInfo;
use App\AnotherClasses\TaximeterConnector;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCarThrowTaximeterTest extends TestCase
{

    public function testExample()
    {
        $carInfo = new CarInfo();

        $carInfo
            ->setBrand("AC")
            ->setModel("378%20GT%20ZAGATO")
            ->setGovNumber("x111xx125")
            ->setColor("Серый")
            ->setVin("11111111111111111")
            ->setCreationYear("1991")
            ->setRegSertificate("2813456789");

        $carCreationResult = TaximeterConnector::createCar($carInfo);

        $this->assertTrue(true);
    }
}
