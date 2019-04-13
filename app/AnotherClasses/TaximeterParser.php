<?php

namespace App\AnotherClasses;

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL);

class TaximeterParser
{

    private static $url = "https://passport.yandex.ru/auth";
    private static $login = 'parkcardisp@yandex.ru'; //Логин
    private static $passwd = 'park188'; //Пароль
    private static $user_cookie_file = ''; //Полный путь до файла, где будем хранить куки
    private static $user_token_file = '';
    private static $idkey = '0EN13471777512SYYmjWcm'; //Хрен знает что
    private static $retpath = ''; //Откуда мы пришли на страницу авторизации
    private static $timestamp = ''; //Хрен знает что
    private static $twoweeks = 'yes'; //Две недели какие-то
    private static $In = 'Войти'; //Кнопка входа

    public static function auth()
    {
        $ch = curl_init(TaximeterParser::$url);

        curl_setopt($ch, CURLOPT_URL,TaximeterParser::$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterParser::$user_cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterParser::$user_cookie_file);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS,"login=".TaximeterParser::$login."&passwd=".TaximeterParser::$passwd);

        // Получение хедера для вытаскивания Session ID
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

    public static function getBalance($phonenumber) {

        TaximeterParser::$user_cookie_file = base_path('resources/cookies.txt');
        TaximeterParser::$user_token_file = base_path('resources/token.txt');

        $yandexDataForAuth = TaximeterParser::auth();

        $token = trim(file_get_contents(TaximeterParser::$user_token_file));

        $url = 'https://fleet.taxi.yandex.ru/drivers/list';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST,1); //Будем отправлять POST запрос
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

        curl_setopt($ch, CURLOPT_POSTFIELDS,"{\"park_id\":\"f25f9892dd5c457394733ffe83fcccab\",\"work_rule_id\":null,\"work_status_id\":\"working\",\"car_categories\":[],\"car_amenities\":[],\"limit\":40,\"offset\":0,\"sort\":[{\"direction\":\"desc\",\"field\":\"account.current.balance\"}],\"text\":\"" . substr($phonenumber,1,10) ."\"}");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json;charset=UTF-8',
            'Cookie: yandexuid=7251759631555130174; Session_id=3:1555130285.5.0.1555130285582:VQR-bQ:4c.1|824038420.0.2|197688.245407.XUORRwuGbVL-J6WsNn1kBd3DeGg;' ,
            'X-CSRF-TOKEN: '.$token
        ));

        curl_setopt($ch, CURLOPT_COOKIEFILE, TaximeterParser::$user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, TaximeterParser::$user_cookie_file); //Подставляем куки два

        curl_setopt($ch, CURLOPT_ENCODING ,"utf-8");

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $html = curl_exec($ch);

        echo $html;
        return;

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($html, 0, $header_size);
        $html = substr($html, $header_size);

        curl_close($ch);

        $token = explode("\r\n", explode("X-CSRF-TOKEN: ", $header)[1])[0];


        file_put_contents(TaximeterParser::$user_token_file, $token);

        return json_decode($html)->data[0]->accounts[0]->balance;
    }
}