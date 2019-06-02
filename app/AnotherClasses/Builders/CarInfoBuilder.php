<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 01.06.19
 * Time: 17:16
 */

namespace App\AnotherClasses\Builders;

class CarInfoBuilder
{
    protected $brand;
    protected $model;
    protected $creation_year;
    protected $color;
    protected $gov_number;
    protected $vin;
    protected $reg_sertificate;

    /**
     * @param mixed $brand
     * @return CarInfoBuilder
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param mixed $model
     * @return CarInfoBuilder
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $creation_year
     * @return CarInfoBuilder
     */
    public function setCreationYear($creation_year)
    {
        $this->creation_year = $creation_year;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreationYear()
    {
        return $this->creation_year;
    }

    /**
     * @param mixed $color
     * @return CarInfoBuilder
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $gov_number
     * @return CarInfoBuilder
     */
    public function setGovNumber($gov_number)
    {
        $this->gov_number = $gov_number;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGovNumber()
    {
        return $this->gov_number;
    }

    /**
     * @param mixed $vin
     * @return CarInfoBuilder
     */
    public function setVin($vin)
    {
        $this->vin = $vin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * @param mixed $reg_sertificate
     * @return CarInfoBuilder
     */
    public function setRegSertificate($reg_sertificate)
    {
        $this->reg_sertificate = $reg_sertificate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegSertificate()
    {
        return $this->reg_sertificate;
    }

}