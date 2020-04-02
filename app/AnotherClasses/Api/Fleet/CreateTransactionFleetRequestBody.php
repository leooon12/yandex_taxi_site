<?php

namespace App\AnotherClasses\Api\Fleet;

/**
 * Тело запроса к функции Transactions
 * @package App\AnotherClasses\Api\Fleet
 */
class CreateTransactionFleetRequestBody extends FleetRequestBuilder
{
    /**
     * Конструктор
     *
     * @param $amount double Сумма к выплате
     * @param $category_id string Идентификатор типа выплаты
     * @param $description string Описание выплаты
     * @param $driver_profile_id string Идентификатор водителя
     * @param $park_id string Идентификатор парка
     */
    public function __construct($amount, $category_id, $description, $driver_profile_id, $park_id)
    {
        parent::__construct();

        $this->setValue('amount', strval($amount));
        $this->setValue('category_id', $category_id);
        $this->setValue('description', $description);
        $this->setValue('driver_profile_id', $driver_profile_id);
        $this->setValue('park_id', $park_id);
    }
}