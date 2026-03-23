<?php

namespace App\Observers;

use App\Models\Exam;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Notice;
use App\Models\StudyMaterial;
use App\Models\User;
use App\Notifications\ExamNotification;
use App\Notifications\AttendanceNotification;
use App\Notifications\StudentNotification;
use App\Notifications\AssignmentNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class NotificationObserver
{
    /**
     * Notify all admins about an event
     */
    protected function notifyAdmins($notification)
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify($notification);
        }
    }

    /**
     * Handle the Exam "created" event.
     */
    public function created(Model $model): void
    {
        try {
            if ($model instanceof Exam) {
                // Notify admins and students about new exam
                \App\Services\NotificationService::notifyExam($model, 'created');
            } 
            elseif ($model instanceof Attendance) {
                // Notify admin about attendance marked
                $this->notifyAdmins(new AttendanceNotification($model->student, ['date' => $model->date, 'status' => $model->status], 'marked'));
                // Notify student's user about attendance marked
                $student = $model->student;
                if ($student && $student->user) {
                    $student->user->notify(new AttendanceNotification($student, ['date' => $model->date, 'status' => $model->status], 'marked'));
                }
            }
            elseif ($model instanceof Student) {
                // Notify admins about new student enrollment
                $this->notifyAdmins(new StudentNotification($model, 'enrolled'));
                // Notify parent about student enrollment  
                $parent = $model->parent;
                if ($parent && $parent->user) {
                    $parent->user->notify(new StudentNotification($model, 'enrolled'));
                }
                // Notify the student's own user
                if ($model->user) {
                    $model->user->notify(new StudentNotification($model, 'enrolled'));
                }
            }
            elseif ($model instanceof Notice) {
                // Notify admins about new notice
                $this->notifyAdmins(new AssignmentNotification($model, 'created'));
                // Notify all student users about new notice
                $students = \App\Models\Student::all();
                foreach ($students as $student) {
                    if ($student->user) {
                        $student->user->notify(new AssignmentNotification($model, 'created'));
                    }
                }
            }
            elseif ($model instanceof StudyMaterial) {
                // Notify admins about new study material
                $this->notifyAdmins(new AssignmentNotification($model, 'created'));
                // Notify students about new study material
                $students = \App\Models\Student::all();
                foreach ($students as $student) {
                    if ($student->user && ($model->subject_id === null || $model->subject_id == $student->current_subject_id)) {
                        $student->user->notify(new AssignmentNotification($model, 'created'));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification on created: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        try {
            if ($model instanceof Exam) {
                // Notify admins and students about exam update
                \App\Services\NotificationService::notifyExam($model, 'updated');
            }
            elseif ($model instanceof Attendance) {
                // Notify admin about attendance update
                $this->notifyAdmins(new AttendanceNotification($model->student, ['date' => $model->date, 'status' => $model->status], 'updated'));
                // Notify student's user about attendance update
                $student = $model->student;
                if ($student && $student->user) {
                    $student->user->notify(new AttendanceNotification($student, ['date' => $model->date, 'status' => $model->status], 'updated'));
                }
            }
            elseif ($model instanceof Student) {
                // Notify admins about student details update
                $this->notifyAdmins(new StudentNotification($model, 'details_updated'));
                // Notify parent about student details update
                $parent = $model->parent;
                if ($parent && $parent->user) {
                    $parent->user->notify(new StudentNotification($model, 'details_updated'));
                }
                // Notify the student's own user
                if ($model->user) {
                    $model->user->notify(new StudentNotification($model, 'details_updated'));
                }
            }
            elseif ($model instanceof StudyMaterial) {
                // Notify admins about study material update
                $this->notifyAdmins(new AssignmentNotification($model, 'updated'));
                // Notify students about study material update
                $students = \App\Models\Student::all();
                foreach ($students as $student) {
                    if ($student->user && ($model->subject_id === null || $model->subject_id == $student->current_subject_id)) {
                        $student->user->notify(new AssignmentNotification($model, 'updated'));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification on updated: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        try {
            if ($model instanceof Exam) {
                // Notify admins and students about exam deletion
                \App\Services\NotificationService::notifyExam($model, 'deleted');
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification on deleted: ' . $e->getMessage());
        }
    }
}
