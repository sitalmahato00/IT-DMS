<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use App\Mail\ResetPasswordMail;

class PasswordResetNotification extends ResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(
            route(
                'password.reset',
                [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ],
                false
            )
        );

        return new ResetPasswordMail($notifiable, $resetUrl);
    }
}
