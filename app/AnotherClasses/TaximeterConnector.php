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
        $url = 'https://fleet.taxi.yandex.ru/api/v1/drivers/create';

        $data = ''.
            '{'.
                '"accounts":{'.
                    '"balance_limit":"-50"'.
                '},'.
                '"driver_profile":{'.
                    '"driver_license":{'.
                        '"country":"'.$driverInfo->getDriverDocumentInfo()->getCountry().'",'.
                        '"number":"'.$driverInfo->getDriverDocumentInfo()->getSerialNumber().$driverInfo->getDriverDocumentInfo()->getUniqNumber().'",'.
                        '"expiration_date":"'.$driverInfo->getDriverDocumentInfo()->getIssueDate().'",'.
                        '"issue_date":"'.$driverInfo->getDriverDocumentInfo()->getEndDate().'",'.
                        '"birth_date":"'.$driverInfo->getBirthdate().'"'.
                    '},'.
                    '"first_name":"'. $driverInfo->getName() .'",'.
                    '"last_name":"'.$driverInfo->getSurname().'",'.
                    '"middle_name":"'. $driverInfo->getPatronymic() .'",'.
                    '"phones":["+7'. substr($driverInfo->getPhone(), 1) . '"],'.
                    '"work_rule_id":"e26a3cf21acfe01198d50030487e046b",'.
                    '"providers":["yandex", "park"],'.
                    '"hire_date":"2019-10-11",'.
                    '"deaf":null,'.
                    '"email":null,'.
                    '"address":null,'.
                    '"comment":null,'.
                    '"check_message":null,'.
                    '"car_id":null,'.
                    '"fire_date":null,'.
                    '"identifications":[],'.
                    '"bank_accounts":[],'.
                    '"tax_identification_number":null,'.
                    '"primary_state_registration_number":null,'.
                    '"emergency_person_contacts":[],'.
                    '"balance_deny_onlycard":false'.
                '}'.
            '}';
        
        return TaximeterConnector::newPost($url, $data);
    }
    
    public static function newPost($url, $data, $method=null) {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');
        $yandexDataForAuth = TaximeterConnector::auth();

        $ch = curl_init($url);

        if (!$method)
            curl_setopt($ch, CURLOPT_POST, 1); //Будем отправлять POST запрос
        else if ($method == "PUT")
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); //Будем отправлять PUT запрос

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: fleet.taxi.yandex.ru',
            'Connection: keep-alive',
            'Origin: https://fleet.taxi.yandex.ru',
            'X-Park-Id: ' . TaximeterConnector::PARK_ID,
            'Content-Type: application/json;charset=UTF-8',
            'Accept: application/json, text/plain, */*',
            'X-Requested-With: XMLHttpRequest',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
            'Sec-Fetch-Site: same-origin',
            //'Referer: https://fleet.taxi.yandex.ru/drivers/create?park=' . TaximeterConnector::PARK_ID,
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: en-US,en;q=0.9,ru;q=0.8',
            'Cookie: '.
            'yandexuid=' . $yandexDataForAuth[1] . '; '.
            'mda=0; '.
            'yandex_gid=56; '.
            'my=YwA=; '.
            '_ym_wasSynced=%7B%22time%22%3A1570794684076%2C%22params%22%3A%7B%22eu%22%3A0%7D%2C%22bkParams%22%3A%7B%7D%7D; '.
            '_ym_uid=1570794684591778930; '.
            '_ym_d=1570794684; '.
            'yabs-frequency=/4/0000000000000000/6huzRSWt9uY4Sd38Do40/; '.
            'zm=m-white_bender.webp.css-https%3As3home-static_IK3XQn5kUbulXjsuV2Fx5xfV4nQ%3Al; '.
            '_ym_isad=2; '.
            '_ym_visorc_784657=b; '.
            'Session_id=' . $yandexDataForAuth[0] . '; '.
            'sessionid2=' . $yandexDataForAuth[2] . '; '.
            'yp=1573386682.ygu.1#1886154877.udn.cDpQYXJrQ2FyRGlzcA%3D%3D; '.
            'ys=udn.cDpQYXJrQ2FyRGlzcA%3D%3D; '.
            'L=fA8Je1ABQUh4e1FVcwgCV2BaXlJjBnd+OAMEBHQFEAY+Hx0=.1570794877.14015.348718.87ece85627ae912e22a8ed7c7e676f5e; '.
            'yandex_login=ParkCarDisp; '.
            'i=3KpCDCTnCtbSuSi4yD+ZW9C7WkLeCa2SYy+hpsBiYvgZUhvGzczY4p8W4mjjV2XTBcBQhrbS8IUmB4cBPkCO5Vvs/L4=; '.
            'cycada=BmitPq4LpOJni8brYHL4iJupRAJ7N+/wIGgr0XPVauw=; '.
            'park_id=' . TaximeterConnector::PARK_ID . '; '.
            '_ym_visorc_51171164=w; '.
            'user_lang=ru'
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return json_decode($html, true);
    }

    public static function createCar(CarInfo $carInfo) {
        $url = 'https://fleet.taxi.yandex.ru/api/v1/cars/create';

        $data = ''.
            '{'.
                '"status":                  "working",'.
                '"brand":                   "'. $carInfo->getBrand()            .'",'.
                '"model":                   "'. $carInfo->getModel()            .'",'.
                '"color":                   "'. $carInfo->getColor()            .'",'.
                '"year":                     '. $carInfo->getCreationYear()     .','.
                '"number":                  "'. $carInfo->getGovNumber()        .'",'.
                '"callsign":                "'. $carInfo->getCallSign()         .'",'.
                '"vin":                     "'. $carInfo->getVin()              .'",'.
                '"registration_cert":       "'. $carInfo->getRegSertificate()   .'",'.
                '"booster_count":           0,'.
                '"categories":              [],'.
                '"carrier_permit_owner_id": null,'.
                '"transmission":            "unknown",'.
                '"rental":                  null,'.
                '"chairs":                  [],'.
                '"tariffs":                 [],'.
                '"cargo_loaders":           0,'.
                '"carrying_capacity":       null,'.
                '"body_number":             null,'.
                '"amenities":               [],'.
                '"permit_num":              null,'.
                '"categories":              ["econom","comfort","comfort_plus","start","standart","express"],'.
                '"amenities":               ["conditioner","animals","delivery","cargo_clean"]'.
            '}';
        
        return TaximeterConnector::newPost($url, $data);
    }
    
    public static function changeCar($driverId, $carId) {
        $url = 'https://fleet.taxi.yandex.ru/api/v1/drivers/car-bindings';

        $data = ''.
            '{'.
                '"driver_id":"'. $driverId .'",'.
                '"car_id":"'. $carId .'"'.
            '}';
        
        return TaximeterConnector::newPost($url, $data, "PUT");
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
