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
     * @return string Строковое представление ответа на запрос
     */
    public function getResponse()
    {
        $headers = array();
        $headers[] = 'X-Api-Key: '.$this->api_key;
        $headers[] = 'X-Client-ID: '.$this->client_id;

        return $this->getResponseBase($this->url, $headers, $this->request_body);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return  $this->request_body;
    }
}