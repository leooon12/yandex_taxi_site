<?php
namespace App\Jobs;

class SendRegistrationSms extends BaseSms
{
    const MESSAGE = "Код для подтверждения регистрации: ";
    public function __construct($phoneNumber, $code)
    {
        parent::__construct($phoneNumber);
        $this->text .= self::MESSAGE . $code;
    }
}
