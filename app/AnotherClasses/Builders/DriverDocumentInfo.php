<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 01.06.19
 * Time: 17:35
 */

namespace App\AnotherClasses\Builders;


class DriverDocumentInfo
{
    protected $serial_number;
    protected $uniq_number;
    protected $issue_date;
    protected $end_date;
    protected $county;

    /**
     * @param mixed $serial_number
     * @return DriverDocumentInfo
     */
    public function setSerialNumber($serial_number)
    {
        $this->serial_number = $serial_number;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    /**
     * @param mixed $uniq_number
     * @return DriverDocumentInfo
     */
    public function setUniqNumber($uniq_number)
    {
        $this->uniq_number = $uniq_number;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUniqNumber()
    {
        return $this->uniq_number;
    }

    /**
     * @param mixed $issue_date
     * @return DriverDocumentInfo
     */
    public function setIssueDate($issue_date)
    {
        $this->issue_date = substr($issue_date, 0, 10);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssueDate()
    {
        return $this->issue_date;
    }

    /**
     * @param mixed $end_date
     * @return DriverDocumentInfo
     */
    public function setEndDate($end_date)
    {
        $this->end_date = substr($end_date, 0, 10);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param mixed $county
     * @return DriverDocumentInfo
     */
    public function setCounty($county)
    {
        $this->county = $county;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCounty()
    {
        return $this->county;
    }
}