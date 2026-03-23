<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentAccountNotification extends Notification
{
    use Queueable;

    protected $password;
    protected $role;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $password, string $role = 'student')
    {
        $this->password = $password;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->view('emails.student-account', [
                'notifiable' => $notifiable,
                'password' => $this->password,
            ])
            ->subject("Your Student Account Credentials - IT Department");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Account Created',
            'message' => 'Your ' . $this->role . ' account has been created. Please check your email for login credentials.',
            'role' => $this->role,
        ];
    }
}

