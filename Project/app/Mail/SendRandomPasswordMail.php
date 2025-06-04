<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRandomPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $randomPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($randomPassword)
    {
        $this->randomPassword = $randomPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Password Sementara Akun Anda')
                    ->view('emails.random_password')
                    ->with([
                        'password' => $this->randomPassword,
                    ]);
    }
}
