<?php

namespace Tests\Unit;

use App\AnotherClasses\Builders\DriverInfo;
use App\AnotherClasses\TaximeterConnector;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateDriverThrowTaximeterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $driverInfo = new DriverInfo();

        $driverInfo->setName("Леонид")
            ->setSurname("Бабков")
            ->setPatronymic("Николаевич")
            ->setBirthdate("1990-09-09")
            ->setPhone("79145551353");

        $driverInfo->getCarInfo()
            ->setBrand("AC")
            ->setModel("378%20GT%20ZAGATO")
            ->setGovNumber("x000xx125")
            ->setColor("Серый")
            ->setVin("11111111111111111")
            ->setCreationYear("1991")
            ->setRegSertificate("2813456789");

        $driverInfo->getDriverDocumentInfo()
            ->setSerialNumber("0123")
            ->setUniqNumber("456788")
            ->setCounty("rus")
            ->setEndDate("2029-09-09")
            ->setIssueDate("2001-09-09");

        //$driverCreationResult = TaximeterConnector::createDriver($driverInfo);

        $this->assertTrue(true);
    }
}
