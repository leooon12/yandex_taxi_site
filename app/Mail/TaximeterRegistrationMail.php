<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaximeterRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $driver_info;
    protected $phone;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($driver_info, $phone)
    {
        $this->driver_info = $driver_info;
        $this->phone = $phone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.taximeter_registration')->subject('Новая заявка водителя')
            ->with([
                "driver_info" => $this->driver_info,
                "phone" => $this->phone
            ]);
    }
}
