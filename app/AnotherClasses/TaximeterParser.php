<?php

namespace App\AnotherClasses;

ini_set("display_errors", 1);
ini_set('error_reporting', E_ALL);

class TaximeterParser
{

    private static $url = "https://passport.yandex.ru/auth";
    private static $login = ''; //Логин
    private static $passwd = ''; //Пароль
    private static $user_cookie_file = 'cookies.txt'; //Полный путь до файла, где будем хранить куки
    private static $idkey = '0EN13471777512SYYmjWcm'; //Хрен знает что
    private static $retpath = ''; //Откуда мы пришли на страницу авторизации
    private static $timestamp = ''; //Хрен знает что
    private static $twoweeks = 'yes'; //Две недели какие-то
    private static $In = 'Войти'; //Кнопка входа

    public static function getData() {

        self::auth(self::$url); // Авторизируемся.
        self::selectPark();
        $driverId = self::findDriverId($_GET["query"]);
        $balance = self::getBalanceByDriverId($driverId);

       return $balance;
    }

    public static function auth($url)
    {
        global $user_cookie_file, $idkey, $retpath, $timestamp, $login, $passwd, $twoweeks, $In; // Получаем все POST данные
        /* Небольшая прелюдия с инифиализацией cURL и прочей шулухой */
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); //Куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file); //Куки два
        curl_setopt($ch, CURLOPT_POST, 1); //Будем отправлять POST запрос
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, "idkey=$idkey&retpath=$retpath&timestamp=$timestamp&login=$login&passwd=$passwd&twoweeks=$twoweeks&In=$In&display=page");

        $html = curl_exec($ch);

        curl_close($ch);

        return $html; //Возвращаем ответ Яндекса
    }

    public static function selectPark()
    {
        $url = 'https://lk.taximeter.yandex.ru/login?db=f25f9892dd5c457394733ffe83fcccab';

        $user_cookie_file = 'cookies.txt'; //Получаем сохраненный после авторизации файл с куками.
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $html = curl_exec($ch);

        curl_close($ch);

        return $html; //Возвращаем ответ
    }

    public static function findDriverId($searchText)
    {
        $url = 'https://lk.taximeter.yandex.ru/dictionary/items/drivers?filter=all&rule=&is_timeblock=false&is_chair=false&is_buster=false&q=' . urlencode($searchText) . '&category=0&service=0&date_last_order=';

        $user_cookie_file = 'cookies.txt'; //Получаем сохраненный после авторизации файл с куками.

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            ':authority: lk.taximeter.yandex.ru',
            ':method: POST',
            ':path: /dictionary/items/drivers',
            ':scheme: https',
            'accept: text/html',
            'accept-charset: utf-8',
            'accept-encoding: gzip, deflate, br',
            'accept-language: en-US,en;q=0.9,ru;q=0.8',
            'content-length: 0',
            'content-type: application/x-www-form-urlencoded; charset=UTF-8',
            'cookie: yandexuid=2781907721554184636; i=hBJNiKJtZBdXXC/0qeCkRSDGg2Hrkm6Sf7YGxHreCFz2QE/zRtF8tY8becz3lIkPfuEsgfyOYkEiL+856Bq2JD+z15c=; _ym_wasSynced=%7B%22time%22%3A1554184647450%2C%22params%22%3A%7B%22eu%22%3A0%7D%2C%22bkParams%22%3A%7B%7D%7D; _ym_uid=1554184647371772611; _ym_d=1554184647; mda=0; _ym_isad=2; _ym_visorc_44292669=w; _ym_visorc_784657=b; Session_id=3:1554184669.5.0.1554184669682:VQR-bQ:51.1|824038420.0.2|197152.715309.Oq9oPLJFuLktsbSq5HEsl7Px1jw; sessionid2=3:1554184669.5.0.1554184669682:VQR-bQ:51.1|824038420.0.2|197152.2716.FZu-drArsSWg4bUgreLtNyV5IJg; yp=1869544636.yrts.1554184636#1869544636.yrtsi.1554184636#1869544669.udn.cDpQYXJrQ2FyRGlzcA%3D%3D; ys=udn.cDpQYXJrQ2FyRGlzcA%3D%3D; L=fn1mcVtUVkhFCk5HU31qUWN5S2kEZVlNCS4DXzs2BnAzMUc=.1554184669.13823.318700.1623d36d93b1b0243d6fd5f56e670bd6; yandex_login=ParkCarDisp; .AspNetCore.Antiforgery.uGYhvnAGzi0=CfDJ8Pj1S8DdfZlAigez8TXM6pAlgbkDQYmB1JjnAfta3VM4yPIaicKfUl7MDGOjwFmXo93o-uOY97_j2_WRVljHkBgwSomH-YDpkxrqGseuLggJ1izxastc4vuzdhZ8NgGgM4x91j1wdQOl9-bCACz-VU8; user_login=ParkCarDisp; _ym_visorc_51171164=w; user_db=1a14b9b3ec0248f1bb63e00fad3e6e67; hide_menu=false; .AspNetCore.Culture=c%3Dru%7Cuic%3Den; YandexPassport.Auth=CfDJ8Pj1S8DdfZlAigez8TXM6pD35HLk2jotNn3ZnAXTkNyWnDcaW%2FJj4iazgxc0Np8ca%2BM30FA8d%2FoSZ7lHVXNCDJ2X6fB%2B7Gdd4ZwQWVIErYZgmLOR7gDwZNLlnmh0pK1Cz883PolNevrs7XLwn5UUx1w6cUbOiAW1n%2FsZavwgMekCroembHSrUrGHKoOiiLe9lBFhjpjqVO9ang%2BkTSkVSlW38F5B5GfoSlSJSyuynpCs2Sw0VNqFivcvUhaDP3Dc5ydMGKXx%2FIJXdSR1%2FLRgVFzVTwWAvsQ6QRtmxto9orBpTlrS9rwajqilGsioHHcqOQ%3D%3D',
            'origin: https://lk.taximeter.yandex.ru',
            'referer: https://lk.taximeter.yandex.ru/dictionary/drivers?db=1a14b9b3ec0248f1bb63e00fad3e6e67&lang=en&hide_menu=false',
            'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            'x-requested-with: XMLHttpRequest',
            'x-taximeter-antiforgery: CfDJ8Pj1S8DdfZlAigez8TXM6pDLGRULEaZYZ5cL6_zJ7XXLap7XdpFmqcHLeNPaPysswLLMMXPzT0JrDKHxu_kMfpOzttU5LDF1iy25VvTQqu4G4n4BxL6MDRUlCxf_Xh5KX2eSHFNHaPFrVdpLMCH1arTJLtSZkBDt2FDyyXcy-33UqgfAdSCdOpXaIpdbSb-a_A'
        ));

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "filter=all&rule=&is_timeblock=false&is_chair=false&is_buster=false&q=5&category=0&service=0&date_last_order=");
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");


        $server_output = curl_exec($ch);

        curl_close($ch);

        return explode('"', explode('data-guid="', $server_output)[1])[0];
    }

    public static function getBalanceByDriverId($driverId)
    {
        $url = 'https://lk.taximeter.yandex.ru/driver/items/pays?driver=' . $driverId;

        $user_cookie_file = 'cookies.txt'; //Получаем сохраненный после авторизации файл с куками.

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            ':authority: lk.taximeter.yandex.ru',
            ':method: POST',
            ':path: /dictionary/items/drivers',
            ':scheme: https',
            'accept: text/html',
            'accept-charset: utf-8',
            'accept-encoding: gzip, deflate, br',
            'accept-language: en-US,en;q=0.9,ru;q=0.8',
            'content-length: 0',
            'content-type: application/x-www-form-urlencoded; charset=UTF-8',
            'cookie: yandexuid=2781907721554184636; i=hBJNiKJtZBdXXC/0qeCkRSDGg2Hrkm6Sf7YGxHreCFz2QE/zRtF8tY8becz3lIkPfuEsgfyOYkEiL+856Bq2JD+z15c=; _ym_wasSynced=%7B%22time%22%3A1554184647450%2C%22params%22%3A%7B%22eu%22%3A0%7D%2C%22bkParams%22%3A%7B%7D%7D; _ym_uid=1554184647371772611; _ym_d=1554184647; mda=0; _ym_isad=2; _ym_visorc_44292669=w; _ym_visorc_784657=b; Session_id=3:1554184669.5.0.1554184669682:VQR-bQ:51.1|824038420.0.2|197152.715309.Oq9oPLJFuLktsbSq5HEsl7Px1jw; sessionid2=3:1554184669.5.0.1554184669682:VQR-bQ:51.1|824038420.0.2|197152.2716.FZu-drArsSWg4bUgreLtNyV5IJg; yp=1869544636.yrts.1554184636#1869544636.yrtsi.1554184636#1869544669.udn.cDpQYXJrQ2FyRGlzcA%3D%3D; ys=udn.cDpQYXJrQ2FyRGlzcA%3D%3D; L=fn1mcVtUVkhFCk5HU31qUWN5S2kEZVlNCS4DXzs2BnAzMUc=.1554184669.13823.318700.1623d36d93b1b0243d6fd5f56e670bd6; yandex_login=ParkCarDisp; .AspNetCore.Antiforgery.uGYhvnAGzi0=CfDJ8Pj1S8DdfZlAigez8TXM6pAlgbkDQYmB1JjnAfta3VM4yPIaicKfUl7MDGOjwFmXo93o-uOY97_j2_WRVljHkBgwSomH-YDpkxrqGseuLggJ1izxastc4vuzdhZ8NgGgM4x91j1wdQOl9-bCACz-VU8; user_login=ParkCarDisp; _ym_visorc_51171164=w; user_db=1a14b9b3ec0248f1bb63e00fad3e6e67; hide_menu=false; .AspNetCore.Culture=c%3Dru%7Cuic%3Den; YandexPassport.Auth=CfDJ8Pj1S8DdfZlAigez8TXM6pD35HLk2jotNn3ZnAXTkNyWnDcaW%2FJj4iazgxc0Np8ca%2BM30FA8d%2FoSZ7lHVXNCDJ2X6fB%2B7Gdd4ZwQWVIErYZgmLOR7gDwZNLlnmh0pK1Cz883PolNevrs7XLwn5UUx1w6cUbOiAW1n%2FsZavwgMekCroembHSrUrGHKoOiiLe9lBFhjpjqVO9ang%2BkTSkVSlW38F5B5GfoSlSJSyuynpCs2Sw0VNqFivcvUhaDP3Dc5ydMGKXx%2FIJXdSR1%2FLRgVFzVTwWAvsQ6QRtmxto9orBpTlrS9rwajqilGsioHHcqOQ%3D%3D',
            'origin: https://lk.taximeter.yandex.ru',
            'referer: https://lk.taximeter.yandex.ru/dictionary/drivers?db=1a14b9b3ec0248f1bb63e00fad3e6e67&lang=en&hide_menu=false',
            'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            'x-requested-with: XMLHttpRequest',
            'x-taximeter-antiforgery: CfDJ8Pj1S8DdfZlAigez8TXM6pDLGRULEaZYZ5cL6_zJ7XXLap7XdpFmqcHLeNPaPysswLLMMXPzT0JrDKHxu_kMfpOzttU5LDF1iy25VvTQqu4G4n4BxL6MDRUlCxf_Xh5KX2eSHFNHaPFrVdpLMCH1arTJLtSZkBDt2FDyyXcy-33UqgfAdSCdOpXaIpdbSb-a_A'
        ));

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $user_cookie_file); //Подставляем куки раз
        curl_setopt($ch, CURLOPT_COOKIEJAR, $user_cookie_file); //Подставляем куки два
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");

        $server_output = curl_exec($ch);

        curl_close($ch);

        $parts = explode('<td class="r">', $server_output);

        $balance = $parts[1];

        $balance = str_replace(",", ".", str_replace("&#xA0;", "", trim(explode("</div>", explode("<div>", $balance)[1])[0])));

        return $balance;
    }
}