<?php

namespace App\Notifications;

use App\Models\Exam;
use App\Notifications\Concerns\UsesNotificationEmailSettings;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamNotification extends Notification
{
    use UsesNotificationEmailSettings;

    protected $exam;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(Exam $exam, string $action = 'created')
    {
        $this->exam = $exam;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->notificationEmailEnabled('notification_email_exam')
            ? ['database', 'mail']
            : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Exam Notification - ' . $this->exam->exam_name)
            ->greeting('Exam ' . ucfirst($this->action))
            ->line($this->toArray($notifiable)['message'])
            ->action('View Exam', route('admin.exam.show', $this->exam->id));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $title = match($this->action) {
            'created' => "New Exam Created: {$this->exam->exam_name}",
            'updated' => "Exam Updated: {$this->exam->exam_name}",
            'deleted' => "Exam Deleted: {$this->exam->exam_name}",
            default => "Exam {$this->action}: {$this->exam->exam_name}",
        };

        $message = match($this->action) {
            'created' => "A new exam '{$this->exam->exam_name}' has been created for {$this->exam->semester} semester on {$this->exam->exam_date->format('M d, Y')}.",
            'updated' => "The exam '{$this->exam->exam_name}' has been updated.",
            'deleted' => "The exam '{$this->exam->exam_name}' has been deleted.",
            default => "Exam action: {$this->action}",
        };

        return [
            'title' => $title,
            'message' => $message,
            'exam_id' => $this->exam->id,
            'exam_name' => $this->exam->exam_name,
            'action' => $this->action,
            'url' => route('admin.exam.show', $this->exam->id),
        ];
    }
}
