<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailAttribute()], false));

        return (new MailMessage)
            ->subject('Restablecer contraseña')
            ->view('emails.reset_password', [
                'name' => $notifiable->nombres ?? $notifiable->getEmailAttribute(),
                'url' => $url,
                'token' => $this->token,
                'notifiable' => $notifiable,
            ]);
    }
}
