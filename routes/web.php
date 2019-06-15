<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\AnotherClasses\Builders\DriverInfo;
use App\AnotherClasses\TaximeterConnector;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('/driver', 'DriverController', ['only' => [
    'index', 'store'
]]);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('withdrawal/','AdminPanelWithdrawalController@index');
    Route::get('edit_request/','AdminRequestController@index');
});


Route::get('/test', function () {
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
        ->setCountry("rus")
        ->setEndDate("2029-09-09")
        ->setIssueDate("2001-09-09");

    $driverCreationResult = TaximeterConnector::createDriver($driverInfo);
    dump($driverCreationResult);
});