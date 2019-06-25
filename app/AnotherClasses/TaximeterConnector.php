<?php

namespace App\AnotherClasses;

use App\AnotherClasses\Builders\CarInfo;
use App\AnotherClasses\Builders\DriverInfo;
use App\AnotherClasses\Builders\FullDriverInfo;


class TaximeterConnector
{
    private static $login = 'parkcardisp@yandex.ru';
    private static $passwd = 'park188';
    private static $user_cookie_file = '';

    const LK_URL = "https://lk.taximeter.yandex.ru";
    const FLEET_URL = "https://fleet.taxi.yandex.ru";
    const PARK_ID = "f25f9892dd5c457394733ffe83fcccab";

    // Типовые запросы

    public static function fleetPostInfoReq($postfields, $url) {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');

        $yandexDataForAuth = TaximeterConnector::auth();

        $token = TaximeterConnector::getFleetToken();

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1); //Будем отправлять POST запрос
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8',
            'Cookie: yandexuid=' . $yandexDataForAuth[1] . '; Session_id=' . $yandexDataForAuth[0] . ';',
            'X-CSRF-TOKEN: ' . $token
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_URL, $url); $html = curl_exec($ch);

        curl_close($ch);

        return json_decode($html, true);
    }

    public static function lkGetReq($url) {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');
        $yandexDataForAuth = TaximeterConnector::auth();

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded;',
            'Cookie: yandexuid=' . $yandexDataForAuth[1] . '; Session_id=' . $yandexDataForAuth[0] . '; sessionid2=' . $yandexDataForAuth[2] . ';yandex_login=ParkCarDisp; _ym_isad=1; user_login=ParkCarDisp; user_db=' . TaximeterConnector::PARK_ID . ';',
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return $html;
    }

    public static function lkPostRequest($postfields, $url)
    {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');
        $yandexDataForAuth = TaximeterConnector::auth();

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1); //Будем отправлять POST запрос
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8;',
            'Cookie: yandexuid=' . $yandexDataForAuth[1] . '; Session_id=' . $yandexDataForAuth[0] . '; sessionid2=' . $yandexDataForAuth[2] . ';yandex_login=ParkCarDisp; _ym_isad=1; user_login=ParkCarDisp; user_db=' . TaximeterConnector::PARK_ID . ';',
            'accept: */*;',
            'accept-encoding: gzip, deflate, br;',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7;',
            'x-requested-with: XMLHttpRequest',
            'dnt: 1',
            ':authority: lk.taximeter.yandex.ru',
            ':method: POST',
            ':path: /create/driver?db=' . TaximeterConnector::PARK_ID . '&hide_menu=true&lang=ru',
            ':scheme: https'
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return json_decode($html, true);
    }


    // Авторизация и выбор парка

    public static function auth()
    {
        $url = "https://passport.yandex.ru/auth";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "login=" . TaximeterConnector::$login . "&passwd=" . TaximeterConnector::$passwd);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $html = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $header = substr($html, 0, $header_size);
        $html = substr($html, $header_size);

        TaximeterConnector::selectPark();

        $sessionId = explode("\n", explode("Session_id\t", file_get_contents(base_path('resources/cookies.txt')))[1])[0];
        $yandexuid = explode("\n", explode("yandexuid\t", file_get_contents(base_path('resources/cookies.txt')))[1])[0];
        $sessionId2 = explode("\n", explode("sessionid2\t", file_get_contents(base_path('resources/cookies.txt')))[1])[0];

        return [$sessionId, $yandexuid, $sessionId2];
    }

    public static function selectPark()
    {
        $url = TaximeterConnector::LK_URL . '/login?db=' . TaximeterConnector::PARK_ID . '';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return $html;
    }


    // Получение данных

    public static function getDriverProfile($phonenumber)
    {
        $url = TaximeterConnector::FLEET_URL . '/drivers/list';
        $postfields = "{\"park_id\":\"" . TaximeterConnector::PARK_ID . "\",\"work_rule_id\":null,\"work_status_id\":\"working\",\"car_categories\":[],\"car_amenities\":[],\"limit\":40,\"offset\":0,\"sort\":[{\"direction\":\"desc\",\"field\":\"account.current.balance\"}],\"text\":\"" . substr($phonenumber, 1, 10) . "\"}";

        $driversData = TaximeterConnector::fleetPostInfoReq($postfields, $url);
        $profiles = isset($driversData['data']['driver_profiles']) ? $driversData['data']['driver_profiles'] : [];

        return count($profiles) > 0 ? $profiles[0] : null;
    }

    public static function getCar($gov_number)
    {
        $url = TaximeterConnector::FLEET_URL . '/vehicle/list';
        $postfields = "{\"park_id\":\"" . TaximeterConnector::PARK_ID . "\",\"limit\":1,\"offset\":0,\"text\":\"" . $gov_number . "\",\"categories\":[],\"amenities\":[],\"statuses\":[],\"sort\":[{\"direction\":\"asc\",\"field\":\"car.call_sign\"}]}";

        $carData = TaximeterConnector::fleetPostInfoReq($postfields, $url);
        return isset($carData['data']) ? $carData['data'][0] : null;
    }

    public static function getBalance($phonenumber){
        $profile = TaximeterConnector::getDriverProfile($phonenumber);
        return isset($profile) ? $profile['accounts'][0]['balance'] : null;
    }

    public static function getCarModels($brandName)
    {
        return json_decode(TaximeterConnector::lkGetReq(TaximeterConnector::LK_URL . '/selector/models?id=' . $brandName), true);
    }

    public static function getAdditionalDriverInfo($phonenumber) {

        $driverInfo = TaximeterConnector::getDriverProfile($phonenumber);

        if (!isset($driverInfo))
            return [null, null, null];

        $html = TaximeterConnector::lkGetReq(TaximeterConnector::LK_URL . '/driver/' . $driverInfo['driver']['id'] . '?db=' . TaximeterConnector::PARK_ID . '&lang=ru');

        $version = explode("\"", explode("name=\"Version\" readonly=\"True\" type=\"text\" value=\"", $html)[1])[0];
        $imei = explode("\"", explode("name=\"Imei\" readonly=\"True\" type=\"text\" value=\"", $html)[1])[0];
        $password = explode("\"", explode("value=\"", explode("Password", $html)[2])[1])[0];

        return [$version, $imei, $password];
    }


    // Необходимые в запросах токены

    public static function getFleetToken()
    {
        $url = TaximeterConnector::FLEET_URL . '/drivers?park_id=' . TaximeterConnector::PARK_ID;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        $html = curl_exec($ch);

        curl_close($ch);

        $token = null;

        if (isset(explode("csrf-token\" content=\"", $html)[1]))
            $token = explode("\">", explode("csrf-token\" content=\"", $html)[1])[0];

        return $token;
    }

    public static function getNewDriverIdAndLkToken()
    {
        $html = TaximeterConnector::lkGetReq(TaximeterConnector::LK_URL . '/create/driver?db=' . TaximeterConnector::PARK_ID);

        $id = explode("\"", explode("value=\"", $html)[1])[0];
        $token = explode("\"", explode("__RequestVerificationToken\" type=\"hidden\" value=\"", $html)[1])[0];

        return [$id, $token];
    }


    // Запросы на создание и изменение

    public static function createDriver(DriverInfo $driverInfo)
    {
        $newDriverIdAndToken = TaximeterConnector::getNewDriverIdAndLkToken();

        $url = TaximeterConnector::LK_URL . '/create/driver?db=' . TaximeterConnector::PARK_ID . '&hide_menu=true&lang=ru';

        $postfields = strtr(
            "DriverModel.Driver.Password="                . $newDriverIdAndToken[0] .
            "&Car.OwnerId=
            &Car.PermitNumber=
            &Car.PermitSeries=
            &Car.PermitDocument=
            &DriverModel.Driver.Address=
            &DriverModel.Driver.Email=
            &Car.EuroCarSegment=
            &Car.Description=
            &DriverModel.Driver.RuleId=e26a3cf21acfe01198d50030487e046b
            &DriverModel.Driver.BalanceLimit=-50
            &DriverModel.Driver.Providers=1
            &DriverModel.Driver.Providers=2
            &Car.Category.Econom=true
            &Car.Category.Comfort=true
            &Car.Category.ComfortPlus=true
            &Car.Category.Start=true
            &Car.Category.Standard=true
            &Car.Transmission=Unknown
            &Car.BoosterCount=0
            &__chairCount=0" .
            "&DriverModel.Driver.LicenseDriverBirthDate=" . $driverInfo->getBirthdate() .
            "&DriverModel.Driver.FirstName="              . $driverInfo->getName() .
            "&DriverModel.Driver.LastName="               . $driverInfo->getSurname() .
            "&DriverModel.Driver.MiddleName="             . $driverInfo->getPatronymic() .
            "&DriverModel.Driver.PhonesFormatted="        . $driverInfo->getPhone() .
            "&DriverModel.Driver.LicenseSeries="          . $driverInfo->getDriverDocumentInfo()->getSerialNumber() .
            "&DriverModel.Driver.LicenseNumber="          . $driverInfo->getDriverDocumentInfo()->getUniqNumber() .
            "&DriverModel.Driver.LicenseIssueDate="       . $driverInfo->getDriverDocumentInfo()->getIssueDate() .
            "&DriverModel.Driver.LicenseExpireDate="      . $driverInfo->getDriverDocumentInfo()->getEndDate() .
            "&DriverModel.Driver.LicenseCountryId="       . $driverInfo->getDriverDocumentInfo()->getCountry() .
            "&Car.Callsign="                              . $driverInfo->getCarInfo()->getCallSign() .
            "&Car.Brand="                                 . $driverInfo->getCarInfo()->getBrand() .
            "&Car.Model="                                 . $driverInfo->getCarInfo()->getModel() .
            "&Car.Year="                                  . $driverInfo->getCarInfo()->getCreationYear() .
            "&Car.Color="                                 . $driverInfo->getCarInfo()->getColor() .
            "&Car.Number="                                . $driverInfo->getCarInfo()->getGovNumber() .
            "&Car.Vin="                                   . $driverInfo->getCarInfo()->getVin() .
            "&Car.RegistrationCertificate="               . $driverInfo->getCarInfo()->getRegSertificate() .
            "&__RequestVerificationToken="                . $newDriverIdAndToken[1] .
            "&X-Requested-With=XMLHttpRequest",
            array("\n" => "", " " => ""));

        return TaximeterConnector::lkPostRequest($postfields, $url);
    }

    public static function createCar(CarInfo $carInfo) {
        $newDriverIdAndToken = TaximeterConnector::getNewDriverIdAndLkToken();

        $url = TaximeterConnector::LK_URL . '/create/car?db=' . TaximeterConnector::PARK_ID . '&lang=ru';

        $postfields = strtr(
            "Car.OwnerId=
            &Car.PermitNumber=
            &Car.PermitSeries=
            &Car.PermitDocument=
            &DriverModel.Driver.Address=
            &DriverModel.Driver.Email=
            &Car.EuroCarSegment=
            &Car.Description=
            &Car.Category.Econom=true
            &Car.Category.Comfort=true
            &Car.Category.ComfortPlus=true
            &Car.Category.Start=true
            &Car.Transmission=Unknown
            &Car.BoosterCount=0
            &__chairCount=0" .
            "&Car.Callsign="                              . $carInfo->getCallSign() .
            "&Car.Brand="                                 . $carInfo->getBrand() .
            "&Car.Model="                                 . $carInfo->getModel() .
            "&Car.Year="                                  . $carInfo->getCreationYear() .
            "&Car.Color="                                 . $carInfo->getColor() .
            "&Car.Number="                                . $carInfo->getGovNumber() .
            "&Car.Vin="                                   . $carInfo->getVin() .
            "&Car.RegistrationCertificate="               . $carInfo->getRegSertificate() .
            "&__RequestVerificationToken="                . $newDriverIdAndToken[1] .
            "&X-Requested-With=XMLHttpRequest",
            array("\n" => "", " " => ""));

        return TaximeterConnector::lkPostRequest($postfields, $url);
    }

    public static function editDriver(FullDriverInfo $driverInfo) {
        $newDriverIdAndToken = TaximeterConnector::getNewDriverIdAndLkToken();

        $url = TaximeterConnector::LK_URL . '/driver/' . $driverInfo->getId() . '?db=' . TaximeterConnector::PARK_ID . '&hide_menu=true&lang=ru';

        $postfields = strtr(
            "Driver.Password="                . $driverInfo->getPassword() .
            "Imei="                           . $driverInfo->getImei() .
            "&Version="                       . $driverInfo->getTaximeterVesion() .
            "&Driver.CarId="                  . $driverInfo->getCarInfo()->getId() .
            "&Driver.FirstName="              . $driverInfo->getName() .
            "&Driver.LastName="               . $driverInfo->getSurname() .
            "&Driver.MiddleName="             . $driverInfo->getPatronymic()  .
            "&Driver.PhonesFormatted="        . $driverInfo->getPhone() .
            "&Driver.RuleId="                 . $driverInfo->getWorkRuleId() .
            "&Driver.Balance="                . $driverInfo->getBalance() .
            "&Driver.BalanceLimit="           . $driverInfo->getBalanceLimit() .
            "&Driver.HireDate="               . $driverInfo->getHireDate() .
            "&Driver.LicenseSeries="          . $driverInfo->getDriverDocumentInfo()->getSerialNumber() .
            "&Driver.LicenseIssueDate="       . $driverInfo->getDriverDocumentInfo()->getIssueDate() .
            "&Driver.LicenseNumber="          . $driverInfo->getDriverDocumentInfo()->getUniqNumber() .
            "&Driver.LicenseExpireDate="      . $driverInfo->getDriverDocumentInfo()->getEndDate() .
            "&Driver.LicenseCountryId="       . $driverInfo->getDriverDocumentInfo()->getCountry() .
            "&Driver.LicenseDriverBirthDate=" . $driverInfo->getBirthdate() .
            "&__RequestVerificationToken="    . $newDriverIdAndToken[1] .
            "&Disabled=false
            &Driver.WorkStatus=2
            &Driver.Deaf=false
            &Driver.BalanceDenyOnlyCard=false
            &Driver.PosTerminal=false
            &Robot.RobotEconom=true
            &Robot.RobotComfort=true
            &Robot.RobotStart=true
            &Robot.RobotStandard=true
            &DisabledMessage=
            &Driver.CheckMessage=
            &Driver.Email=
            &Driver.Comment=
            &Driver.FireDate=
            &Driver.Providers=1
            &Driver.Providers=2
            &Robot.RobotOn=false
            &Driver.Address=
            &X-Requested-With=XMLHttpRequest",
            array("\n" => "", " " => ""));

        return TaximeterConnector::lkPostRequest($postfields, $url);
    }

}