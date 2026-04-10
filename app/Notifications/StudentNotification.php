<?php

namespace App\Notifications;

use App\Models\Student;
use App\Notifications\Concerns\UsesNotificationEmailSettings;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentNotification extends Notification
{
    use UsesNotificationEmailSettings;

    protected $student;
    protected $action;
    protected $data;

    public function __construct(Student $student, string $action, array $data = [])
    {
        $this->student = $student;
        $this->action = $action;
        $this->data = $data;
    }

    public function via(object $notifiable): array
    {
        return $this->notificationEmailEnabled('notification_email_student')
            ? ['database', 'mail']
            : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject('Student Update - ' . ($this->student->user?->name ?? 'Student'))
            ->greeting('Student ' . ucfirst(str_replace('_', ' ', $this->action)))
            ->line($data['message'])
            ->action('Open Student Record', route('admin.students.show', $this->student->id));
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->student->user?->name ?? 'Student';
        $dataSemester = $this->data['semester'] ?? 'next semester';
        $dataStatus = $this->data['status'] ?? 'updated';

        $title = match($this->action) {
            'enrolled' => "{$studentName} Enrolled",
            'promotion' => "Student Promoted: {$studentName}",
            'suspension' => "Student Suspended: {$studentName}",
            'details_updated' => "Student Details Updated: {$studentName}",
            'status_changed' => "Student Status Changed: {$studentName}",
            'alumni' => "Student Marked as Alumni: {$studentName}",
            default => "Student Action: {$this->action}",
        };

        $message = match($this->action) {
            'enrolled' => "Student {$studentName} has been enrolled.",
            'promotion' => "Student {$studentName} has been promoted to {$dataSemester}.",
            'suspension' => "Student {$studentName} has been suspended.",
            'details_updated' => "Details for student {$studentName} have been updated.",
            'status_changed' => "Status changed to {$dataStatus} for {$studentName}.",
            'alumni' => "Student {$studentName} has been marked as alumni.",
            default => "Student action: {$this->action}",
        };

        return [
            'title' => $title,
            'message' => $message,
            'student_id' => $this->student->id,
            'student_name' => $studentName,
            'action' => $this->action,
            'url' => route('admin.students.show', $this->student->id),
        ];
    }
}

