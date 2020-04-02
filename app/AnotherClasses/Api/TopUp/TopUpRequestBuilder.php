<?php

namespace App\AnotherClasses\Api\TopUp;

use App\AnotherClasses\Api\IRequestBuilder;

/**
 * Class TopUpRequestBuilder Помогает формировать запрос к TopUp API
 * @package App\AnotherClasses\Api
 */
class TopUpRequestBuilder implements IRequestBuilder
{
    /**
     * @var string Запрос
     */
    protected $request;

    /**
     * Конструктор
     *
     * @param string $request_template Шаблон запроса
     */
    public function __construct($request_template = "")
    {
        $this->request = $request_template;
    }

    /**
     * @inheritDoc
     */
    public function toString()
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function setValue($name, $value)
    {
        $this->request = str_replace("{".$name."}", $value, $this->request);
    }
}