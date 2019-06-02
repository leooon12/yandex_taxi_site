<?php

namespace App\AnotherClasses;

use App\AnotherClasses\Builders\NewDriverBuilder;
use Illuminate\Support\Facades\Log;

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL);

class TaximeterConnector
{
    private static $login = 'parkcardisp@yandex.ru';
    private static $passwd = 'park188';
    private static $user_cookie_file = '';

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
        $yandexuid = explode("\r\n", explode("yandexuid=", $header)[1])[0];
        $sessionId2 = explode("\n", explode("sessionid2\t", file_get_contents(base_path('resources/cookies.txt')))[1])[0];

        return [$sessionId, $yandexuid, $sessionId2];
    }

    public static function selectPark()
    {
        $url = 'https://lk.taximeter.yandex.ru/login?db=f25f9892dd5c457394733ffe83fcccab';

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

    public static function getToken()
    {
        $url = 'https://fleet.taxi.yandex.ru/drivers?park_id=f25f9892dd5c457394733ffe83fcccab';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        $html = curl_exec($ch);

        curl_close($ch);

        $token = explode("\">", explode("csrf-token\" content=\"", $html)[1])[0];

        return $token;
    }

    public static function getBalance($phonenumber){
        return TaximeterConnector::getDriverProfile($phonenumber)['accounts'][0]['balance'];
    }

    public static function getDriverProfile($phonenumber)
    {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');

        $yandexDataForAuth = TaximeterConnector::auth();

        $token = TaximeterConnector::getToken();

        $url = 'https://fleet.taxi.yandex.ru/drivers/list';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, 1); //Будем отправлять POST запрос
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"park_id\":\"f25f9892dd5c457394733ffe83fcccab\",\"work_rule_id\":null,\"work_status_id\":\"working\",\"car_categories\":[],\"car_amenities\":[],\"limit\":40,\"offset\":0,\"sort\":[{\"direction\":\"desc\",\"field\":\"account.current.balance\"}],\"text\":\"" . substr($phonenumber, 1, 10) . "\"}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8',
            'Cookie: yandexuid=' . $yandexDataForAuth[1] . '; Session_id=' . $yandexDataForAuth[0] . ';',
            'X-CSRF-TOKEN: ' . $token
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        $html = curl_exec($ch);

        curl_close($ch);

        return json_decode($html, true)['data']['driver_profiles'][0];
    }

    public static function getNewDriverIdAndToken()
    {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');

        $yandexDataForAuth = TaximeterConnector::auth();

        $url = 'https://lk.taximeter.yandex.ru/create/driver?db=f25f9892dd5c457394733ffe83fcccab';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded;',
            'Cookie: yandexuid=' . $yandexDataForAuth[1] . '; Session_id=' . $yandexDataForAuth[0] . '; sessionid2=' . $yandexDataForAuth[2] . ';yandex_login=ParkCarDisp; _ym_isad=1; user_login=ParkCarDisp; user_db=f25f9892dd5c457394733ffe83fcccab;',
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        $id = explode("\"", explode("value=\"", $html)[1])[0];
        $token = explode("\"", explode("__RequestVerificationToken\" type=\"hidden\" value=\"", $html)[1])[0];

        return [$id, $token];
    }

    public static function getCarModels($brandName)
    {
        TaximeterConnector::$user_cookie_file = base_path('resources/cookies.txt');

        $yandexDataForAuth = TaximeterConnector::auth();

        $url = 'https://lk.taximeter.yandex.ru/selector/models?id=' . $brandName;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded;',
            'Cookie: yandexuid=' . $yandexDataForAuth[1] . '; Session_id=' . $yandexDataForAuth[0] . '; sessionid2=' . $yandexDataForAuth[2] . ';yandex_login=ParkCarDisp; _ym_isad=1; user_login=ParkCarDisp; user_db=f25f9892dd5c457394733ffe83fcccab;',
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterConnector::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterConnector::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING, "utf-8");

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return json_decode($html, true);
    }

}