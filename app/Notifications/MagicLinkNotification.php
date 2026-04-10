<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MagicLinkNotification extends Notification
{
    use Queueable;

    public string $token;
    public int $expiresMinutes;

    public function __construct(string $token, int $expiresMinutes = 10)
    {
        $this->token = $token;
        $this->expiresMinutes = $expiresMinutes;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('magic.login', ['token' => $this->token]);
        return (new MailMessage)
            ->subject('Your sign-in link')
            ->markdown('emails.magic-link', [
                'url' => $url,
                'cancelUrl' => route('magic.cancel', ['token' => $this->token]),
                'expiresMinutes' => $this->expiresMinutes,
                'notifiable' => $notifiable,
            ]);
    }
}

