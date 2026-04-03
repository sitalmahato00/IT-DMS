<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $code, protected int $expiresMinutes = 10)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your IT-DMS verification code')
            ->greeting('Two-Factor Authentication')
            ->line('Your verification code is: ' . $this->code)
            ->line('This code will expire in ' . $this->expiresMinutes . ' minutes.')
            ->line('If you did not request this login, you can safely ignore this email.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Two-factor code sent',
            'message' => 'A login verification code was sent to your email.',
        ];
    }
}
