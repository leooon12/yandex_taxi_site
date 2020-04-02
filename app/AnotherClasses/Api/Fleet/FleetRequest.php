<?php

namespace App\AnotherClasses\Api\Fleet;

use App\AnotherClasses\Api\ApiPostRequest;

/**
 * Class FleetRequest Запрос к Fleet-API
 * @package App\AnotherClasses\Api\Fleet
 */
class FleetRequest extends FleetRequestBuilder
{
    use ApiPostRequest;

    /**
     * @var string Ключ к Fleet-API
     */
    private $api_key;

    /**
     * @var string Идентификатор клиента Fleet-API
     */
    private $client_id;

    /**
     * @var string Адрес, на который выполняется запрос
     */
    private $url;

    /**
     * @var string Тело запроса
     */
    private $request_body;

    /**
     * Конструктор
     *
     * @param $url string Адрес, на который выполняется запрос
     * @param $api_key string Ключ к Fleet-API
     * @param $client_id string Идентификатор клиента Fleet-API
     */
    public function __construct($url, $api_key, $client_id)
    {
        $this->api_key = $api_key;
        $this->client_id = $client_id;
        $this->url = $url;

        $this->requestBody('');
    }

    /**
     * Сеттер для тела запроса
     *
     * @param $value string Новое значение тела запроса
     */
    public function requestBody($value)
    {
        $this->request_body = $value;
    }

    /**
     * Осуществляет запрос к Fleet-API
     *
     * @param bool $is_idempotency_token_needed Определяет, необходимо ли добавлять токен идемпотентности в заголовок запроса
     *
     * @return string Строковое представление ответа на запрос
     */
    public function getResponse($is_idempotency_token_needed = false)
    {
        $headers = array();
        $headers[] = 'X-Api-Key: '.$this->api_key;
        $headers[] = 'X-Client-ID: '.$this->client_id;

        //Добавления токена идемпотентности при необходимости
        if ($is_idempotency_token_needed)
        {
            $headers[] = 'X-Idempotency-Token: '.$this->createIdempotencyToken();
        }

        return $this->getResponseBase($this->url, $headers, $this->request_body);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return  $this->request_body;
    }

    /**
     * Генеририрует токен идемпотентности в виде hex-строки из 16 байт
     *
     * @return string Токен идемпотентности
     */
    private function createIdempotencyToken()
    {
        //Алгоритм может показаться немного сумеречным, такой он и есть на самом деле
        //Основная проблема в том, что я не особо знаю php ¯\_(ツ)_/¯
        $idempotency_token_length_in_bytes = 16;
        $size_of_int = count(unpack("C*", pack("L", 0)));

        $idempotency_token_bytes = array();

        for ($i = 0; $i < $idempotency_token_length_in_bytes / $size_of_int; ++$i)
        {
            $idempotency_token_bytes = array_merge($idempotency_token_bytes, unpack("C*", pack("L", rand(1, getrandmax()))));
        }

        return bin2hex(join(array_map("chr", $idempotency_token_bytes)));
    }
}