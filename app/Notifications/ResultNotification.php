<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ResultNotification extends Notification
{

    protected $exam;
    protected $totalStudents;

    public function __construct($exam, int $totalStudents = 0)
    {
        $this->exam = $exam;
        $this->totalStudents = $totalStudents;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
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
