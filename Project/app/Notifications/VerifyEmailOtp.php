<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtp extends Notification
{
    use Queueable;

    public function __construct(private string $otp) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your KerjaIn verification code')
            ->line("Enter this code to verify your email:")
            ->line("**{$this->otp}**")
            ->line('Code expires in 10 minutes.');
    }
}
