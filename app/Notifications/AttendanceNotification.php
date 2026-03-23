<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Notifications\Notification;

class AttendanceNotification extends Notification
{

    protected $student;
    protected $attendanceData;
    protected $action;

    public function __construct(Student $student, array $attendanceData = [], string $action = 'marked')
    {
        $this->student = $student;
        $this->attendanceData = $attendanceData;
        $this->action = $action;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $studentName = $this->student->user?->name ?? 'Student';

        $title = match($this->action) {
            'marked' => "Attendance Marked for {$studentName}",
            'updated' => "Attendance Updated for {$studentName}",
            'bulk' => "Bulk Attendance Updated",
            default => "Attendance {$this->action}",
        };

        $message = match($this->action) {
            'marked' => "Attendance has been marked for student {$studentName}.",
            'updated' => "Attendance record for {$studentName} has been updated.",
            'bulk' => "Bulk attendance update has been processed.",
            default => "Attendance action: {$this->action}",
        };

        return [
            'title' => $title,
            'message' => $message,
            'student_id' => $this->student->id,
            'student_name' => $studentName,
            'action' => $this->action,
            'url' => route('admin.attendance'),
        ];
    }
}
