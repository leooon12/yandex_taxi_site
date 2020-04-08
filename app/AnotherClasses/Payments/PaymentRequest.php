<?php

namespace App\AnotherClasses\Payments;

use App\WithdrawalQiwi;

/**
 * Базовый класс для выплат
 * @package App\AnotherClasses\Payments
 */
abstract class PaymentRequest
{
    /**
     * @var WithdrawalBankCard|WithdrawalQiwi Запрос на выплату
     */
    protected $withdrawal;

    /**
     * Конструктор
     *
     * @param $withdrawal WithdrawalBankCard|WithdrawalQiwi Запрос на выплату
     */
    public function __construct($withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    /**
     * @return string Реквизиты для выплаты
     */
    abstract public function getRequisites();

    /**
     * @return string Тип выплаты
     */
    abstract public function getType();

    /**
     * Осуществляет выплату
     *
     * @param $sum double Сумма к выплате
     *
     * @return array Результат осуществления выплаты
     */
    abstract public function makePayment($sum);

    /**
     * @return int Возвращает идентификатор статуса запроса на выплату
     */
    public function getStatus()
    {
        return $this->withdrawal->status_id;
    }

    /**
     * @param $value int Новый идентификатор статуса запроса на выплату
     */
    public function setStatus($value)
    {
        $this->withdrawal->update(['status_id' => $value]);
    }

    /**
     * @return int Сумма к выплате
     */
    public function getSum()
    {
        return $this->withdrawal->sum;
    }

    /**
     * @return int Идентификатор пользователя, запросившего выплату
     */
    public function getUserId()
    {
        return $this->withdrawal->user_id;
    }

    /**
     * @return int Идентификатор запроса на выплату
     */
    public function getWithdrawalId()
    {
        return $this->withdrawal->id;
    }
}