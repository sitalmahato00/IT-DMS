<?php

namespace App\Notifications;

use App\Models\Exam;
use Illuminate\Notifications\Notification;

class ExamNotification extends Notification
{

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
        return ['database'];
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
