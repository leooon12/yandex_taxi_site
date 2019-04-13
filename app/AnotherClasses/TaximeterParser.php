<?php

namespace App\AnotherClasses;

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL);

class TaximeterParser
{

    private static $url = "https://passport.yandex.ru/auth";
    private static $login = 'parkcardisp@yandex.ru';
    private static $passwd = 'park188';
    private static $user_cookie_file = '';

    public static function auth()
    {
        $url = "https://passport.yandex.ru/auth";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterParser::$user_cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterParser::$user_cookie_file);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS,"login=".TaximeterParser::$login."&passwd=".TaximeterParser::$passwd);

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $html = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        $header = substr($html, 0, $header_size);
        $html = substr($html, $header_size);

        $sessionId = explode(";", explode("Session_id=", $header)[1])[0];
        $yandexuid = explode("\r\n", explode("yandexuid=", $header)[1])[0];

        return [$sessionId, $yandexuid];
    }

    public static function getToken(){
        $url = 'https://fleet.taxi.yandex.ru/?park=f25f9892dd5c457394733ffe83fcccab';

        $user_cookie_file = 'cookies.txt'; //Получаем сохраненный после авторизации файл с куками.

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_ENCODING ,"utf-8");

        $html = curl_exec($ch);

        curl_close($ch);

        $token = explode("\">", explode("csrf-token\" content=\"", $html)[1])[0];

        return $token;
    }

    public static function getBalance($phonenumber) {

        TaximeterParser::$user_cookie_file = base_path('resources/cookies.txt');

        $yandexDataForAuth = TaximeterParser::auth();

        $token = TaximeterParser::getToken();

        $url = 'https://fleet.taxi.yandex.ru/drivers/list';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST,1); //Будем отправлять POST запрос
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_POSTFIELDS,"{\"park_id\":\"f25f9892dd5c457394733ffe83fcccab\",\"work_rule_id\":null,\"work_status_id\":\"working\",\"car_categories\":[],\"car_amenities\":[],\"limit\":40,\"offset\":0,\"sort\":[{\"direction\":\"desc\",\"field\":\"account.current.balance\"}],\"text\":\"" . substr($phonenumber,1,10) ."\"}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8',
            'Cookie: yandexuid='.$yandexDataForAuth[1].'; Session_id='.$yandexDataForAuth[0].';' ,
            'X-CSRF-TOKEN: '.$token
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterParser::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterParser::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING ,"utf-8");

        $html = curl_exec($ch);

        curl_close($ch);

        return json_decode($html)->data[0]->accounts[0]->balance;
    }
}