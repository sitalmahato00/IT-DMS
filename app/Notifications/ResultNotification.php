<?php

namespace App\Notifications;

use App\Notifications\Concerns\UsesNotificationEmailSettings;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResultNotification extends Notification
{
    use UsesNotificationEmailSettings;

    protected $exam;
    protected $totalStudents;

    public function __construct($exam, int $totalStudents = 0)
    {
        $this->exam = $exam;
        $this->totalStudents = $totalStudents;
    }

    public function via(object $notifiable): array
    {
        return $this->notificationEmailEnabled('notification_email_result')
            ? ['database', 'mail']
            : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject('Results Published - ' . $this->exam->exam_name)
            ->greeting('Results Published')
            ->line($data['message'])
            ->action('View Marksheet', $data['url'] ?? url('/'));
    }

    public function toArray(object $notifiable): array
    {
        $title = "Results Published: {$this->exam->exam_name}";

        $message = "Results for exam '{$this->exam->exam_name}' have been published. Total students: {$this->totalStudents}";

        return [
            'title' => $title,
            'message' => $message,
            'exam_id' => $this->exam->id,
            'exam_name' => $this->exam->exam_name,
            'action' => 'result_published',
            'url' => route('admin.exam.show', $this->exam->id),
        ];
    }
}

