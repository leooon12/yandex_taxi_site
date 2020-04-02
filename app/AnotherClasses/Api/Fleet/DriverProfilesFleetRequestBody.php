<?php

namespace App\AnotherClasses\Api\Fleet;

/**
 * Тело запроса к функции DriverProfiles
 * @package App\AnotherClasses\Api\Fleet
 */
class DriverProfilesFleetRequestBody extends FleetRequestBuilder
{
    /**
     * Конструктор
     *
     * @param $park_id string Идентификатор парка
     * @param $filter_text string Текст, по которому будут отфильтровываться записи (если текст есть хотя бы в одоном поле, то такая запись будет возвращена)
     */
    public function __construct($park_id, $filter_text)
    {
        parent::__construct();

        $this->setValue('query/park/id', $park_id);

        if ($filter_text != '')
        {
            $this->setValue('query/text', $filter_text);
        }
    }
}