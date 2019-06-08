<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 08.06.19
 * Time: 16:31
 */

namespace App\AnotherClasses\Builders;


class FullDriverInfo extends DriverInfo
{
    protected $password;
    protected $balance;
    protected $balanceLimit;
    protected $id;
    protected $hireDate;
    protected $workRuleId;
    protected $workStatus;
    protected $imei;
    protected $taximeterVesion;

    /**
     * @param mixed $workStatus
     * @return FullDriverInfo
     */
    public function setWorkStatus($workStatus)
    {
        $this->workStatus = $workStatus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWorkStatus()
    {
        return $this->workStatus;
    }

    /**
     * @param mixed $workRuleId
     * @return FullDriverInfo
     */
    public function setWorkRuleId($workRuleId)
    {
        $this->workRuleId = $workRuleId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWorkRuleId()
    {
        return $this->workRuleId;
    }

    /**
     * @param mixed $hireDate
     * @return FullDriverInfo
     */
    public function setHireDate($hireDate)
    {
        $this->hireDate = substr($hireDate, 0, 10);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHireDate()
    {
        return $this->hireDate;
    }

    /**
     * @param mixed $id
     * @return FullDriverInfo
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $balanceLimit
     * @return FullDriverInfo
     */
    public function setBalanceLimit($balanceLimit)
    {
        $this->balanceLimit = $balanceLimit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBalanceLimit()
    {
        return $this->balanceLimit;
    }

    /**
     * @param mixed $balance
     * @return FullDriverInfo
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $password
     * @return FullDriverInfo
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $imei
     * @return FullDriverInfo
     */
    public function setImei($imei)
    {
        $this->imei = $imei;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImei()
    {
        return $this->imei;
    }

    /**
     * @param mixed $taximeterVesion
     * @return FullDriverInfo
     */
    public function setTaximeterVesion($taximeterVesion)
    {
        $this->taximeterVesion = $taximeterVesion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaximeterVesion()
    {
        return $this->taximeterVesion;
    }

    public function setAllFromTaximeterDriverProfile($driverProfile) {
        parent::setAllFromTaximeterDriverProfile($driverProfile);
        $this->setBalance($driverProfile["accounts"][0]['balance']);
        $this->setBalanceLimit($driverProfile["accounts"][0]['balance_limit']);
        $this->setId($driverProfile["driver"]['id']);
        $this->setHireDate($driverProfile["driver"]['hire_date']);
        $this->setWorkRuleId($driverProfile["driver"]['work_rule_id']);
        $this->setWorkStatus($driverProfile["driver"]['work_status']);
    }
}