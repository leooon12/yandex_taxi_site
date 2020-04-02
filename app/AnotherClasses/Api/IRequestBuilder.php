<?php

namespace App\AnotherClasses\Api;

/**
 * Вспомогательный интерфейс, позволяющий сформировать запрос
 * @package App\AnotherClasses\Api
 */
interface IRequestBuilder
{
    /**
     * Устанавливает значение с указанным названием
     *
     * @param $name string Название значения
     * @param $value string Новое значение
     *
     * @return void
     */
    public function setValue($name, $value);

    /**
     * Преобразовывает сформированный запрос в строку
     *
     * @return string Представление запроса в виде строки
     */
    public function toString();
}