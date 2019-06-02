<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 01.06.19
 * Time: 17:37
 */

namespace App\AnotherClasses\Builders;


class NewDriverBuilder
{
    protected $carInfo;
    protected $driverDocumentInfo;
    protected $name;
    protected $surname;
    protected $patronymic;
    protected $phone;
    protected $birthdate;

    public function __construct()
    {
        $this->driverDocumentInfo = new DriverDocumentInfoBuilder();
        $this->carInfo = new CarInfoBuilder();
    }

    /**
     * @return CarInfoBuilder
     */
    public function getCarInfo()
    {
        return $this->carInfo;
    }

    /**
     * @return DriverDocumentInfoBuilder
     */
    public function getDriverDocumentInfo()
    {
        return $this->driverDocumentInfo;
    }

    /**
     * @param mixed $name
     * @return NewDriverBuilder
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $surname
     * @return NewDriverBuilder
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $patronymic
     * @return NewDriverBuilder
     */
    public function setPatronymic($patronymic)
    {
        $this->patronymic = $patronymic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param mixed $phone
     * @return NewDriverBuilder
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $birthdate
     * @return NewDriverBuilder
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }


}