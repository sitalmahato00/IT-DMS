<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification
{

    protected $assignment;
    protected $action;

    public function __construct($assignment, string $action = 'created')
    {
        $this->assignment = $assignment;
        $this->action = $action;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $assignmentTitle = $this->assignment->title ?? ($this->assignment->name ?? 'Assignment');
        $assignmentId = $this->assignment->id ?? 0;

        $title = match($this->action) {
            'created' => "New Assignment: {$assignmentTitle}",
            'updated' => "Assignment Updated: {$assignmentTitle}",
            'deadline' => "Assignment Deadline Reminder: {$assignmentTitle}",
            default => "Assignment {$this->action}",
        };

        $message = match($this->action) {
            'created' => "A new assignment '{$assignmentTitle}' has been added. Due date: {$this->getFormattedDate($this->assignment->due_date ?? null)}",
            'updated' => "The assignment '{$assignmentTitle}' has been updated.",
            'deadline' => "Reminder: Assignment '{$assignmentTitle}' is due on {$this->getFormattedDate($this->assignment->due_date ?? null)}",
            default => "Assignment action: {$this->action}",
        };

        return [
            'title' => $title,
            'message' => $message,
            'assignment_id' => $assignmentId,
            'action' => $this->action,
            'url' => '/admin/assignments/' . $assignmentId,
        ];
    }

    protected function getFormattedDate($date)
    {
        if (!$date) return 'TBD';
        return \Carbon\Carbon::parse($date)->format('M d, Y');
    }
}
