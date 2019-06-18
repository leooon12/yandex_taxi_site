<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 01.06.19
 * Time: 17:37
 */

namespace App\AnotherClasses\Builders;


class DriverInfo
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
        $this->driverDocumentInfo = new DriverDocumentInfo();
        $this->carInfo = new CarInfo();
    }

    /**
     * @return CarInfo
     */
    public function getCarInfo()
    {
        return $this->carInfo;
    }

    /**
     * @return DriverDocumentInfo
     */
    public function getDriverDocumentInfo()
    {
        return $this->driverDocumentInfo;
    }

    /**
     * @param mixed $name
     * @return DriverInfo
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
     * @return DriverInfo
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
     * @return DriverInfo
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
     * @return DriverInfo
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
     * @return DriverInfo
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = substr($birthdate, 0, 10);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function setAllFromTaximeterDriverProfile($driverProfile) {

        $this->setName($driverProfile["driver"]["first_name"])
            ->setSurname($driverProfile["driver"]["last_name"])
            ->setPatronymic($driverProfile["driver"]["middle_name"])
            ->setBirthdate(isset($driverProfile["driver"]["license"]['birth_date']) ? $driverProfile["driver"]["license"]['birth_date'] : null)
            ->setPhone($driverProfile['driver']['phones'][0]);

        // Тут не заполняются поля, которые были при регистрации, ибо их нет: Vin, Данные ПТС...
        $this->getCarInfo()
            ->setBrand($driverProfile["car"]['brand'])
            ->setModel($driverProfile['car']['model'])
            ->setGovNumber($driverProfile['car']['number'])
            ->setColor($driverProfile['car']['color'])
            ->setId($driverProfile['driver']['car_id']);


        $this->getDriverDocumentInfo()
            ->setSerialNumber(substr($driverProfile["driver"]['license']['normalized_number'], 0, 4))
            ->setUniqNumber(substr($driverProfile["driver"]['license']['normalized_number'], 4, 6))
            ->setCountry($driverProfile["driver"]['license']['country'])
            ->setEndDate(substr($driverProfile["driver"]['license']['expiration_date'], 0, 10))
            ->setIssueDate(substr($driverProfile["driver"]['license']['issue_date'], 0, 10));
    }

    /**
     * @param DriverDocumentInfo $driverDocumentInfo
     * @return DriverInfo
     */
    public function setDriverDocumentInfo(DriverDocumentInfo $driverDocumentInfo): DriverInfo
    {
        $this->driverDocumentInfo = $driverDocumentInfo;
        return $this;
    }

    /**
     * @param CarInfo $carInfo
     * @return DriverInfo
     */
    public function setCarInfo(CarInfo $carInfo): DriverInfo
    {
        $this->carInfo = $carInfo;
        return $this;
    }
}