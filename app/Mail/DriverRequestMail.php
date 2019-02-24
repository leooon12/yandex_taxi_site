<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DriverRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $full_name;
    protected $phone_number;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($full_name, $phone_number)
    {
        $this->full_name    = $full_name;
        $this->phone_number = $phone_number;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.driver_request')->subject('Новая заявка водителя')
            ->with([
                'full_name' => $this->full_name,
                'phone_number' => $this->phone_number
            ]);
    }
}
