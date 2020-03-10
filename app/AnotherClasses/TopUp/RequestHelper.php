<?php

namespace App\AnotherClasses\TopUp;

class RequestHelper
{
    protected $_request_string;

    public function __construct($request_string = "")
    {
        $this->_request_string = $request_string;
    }

    public function __toString()
    {
        return $this->_request_string;
    }

    protected function setValue($name, $value)
    {
        $this->_request_string = str_replace("{".$name."}", $value, $this->_request_string);
    }
}