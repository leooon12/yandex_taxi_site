<?php

namespace App\AnotherClasses\Api;

/**
 * Trait Позволяет выполнить POST-запрос
 */
trait ApiPostRequest
{
    /**
     * Выполняет POST-запрос
     *
     * @param string $url Адрес, на который необходимо выполнить запрос
     * @param array $headers Заголовки запроса
     * @param string $body Тело запроса
     *
     * @return string Результат запроса
     */
    public function getResponseBase($url, $headers, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $body
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}