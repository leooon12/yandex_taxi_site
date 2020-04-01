<?php

namespace App\AnotherClasses\Api\Fleet;

use App\AnotherClasses\Api\IRequestBuilder;

/**
 * Class FleetRequestBuilder Помогает формировать запрос к Fleet-API
 * @package App\AnotherClasses\Api\Fleet
 */
class FleetRequestBuilder implements IRequestBuilder
{
    /**
     * @var array Массив со значениями в запросе
     */
    protected $request;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->request = array();
    }

    /**
     * @inheritDoc
     *
     * Предполагается, что значения добавляются в JSON следующим образом:
     * {'foo': {'bar': {'foobar': 'value'}}} <=> setValue('foo/bar/foobar', 'value')
     */
    public function setValue($name, $value)
    {
        $exploded_name = explode('/', $name);
        $exploded_name_length = count($exploded_name);
        $current_value = &$this->request;

        for ($i = 0; $i < $exploded_name_length; ++$i)
        {
            //Если значение не последнее
            if ($i < $exploded_name_length - 1)
            {
                if (!array_key_exists($exploded_name[$i], $current_value))
                {
                    $current_value[$exploded_name[$i]] = array();
                }

                $current_value = &$current_value[$exploded_name[$i]];
            }
            else
            {
                //Последнее значение
                $current_value[$exploded_name[$i]] = $value;
            }
        }
    }

    /**
     * Преобразовывает сформированный запрос в строку
     *
     * @return string Представление запроса в виде строки
     */
    public function toString()
    {
        return json_encode($this->request, JSON_FORCE_OBJECT);
    }
}