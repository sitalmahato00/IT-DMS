<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\ExamNotification;
use App\Notifications\AssignmentNotification;
use App\Notifications\ResultNotification;
use App\Notifications\AttendanceNotification;
use App\Notifications\StudentNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send exam notification to all admins AND all students
     */
    public static function notifyExam($exam, string $action = 'created', ?Collection $users = null)
    {
        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $user) {
            $user->notify(new ExamNotification($exam, $action));
        }

        // Notify students if specific users not provided
        if ($users === null) {
            $students = User::where('role', 'student')->get();
            foreach ($students as $user) {
                $user->notify(new ExamNotification($exam, $action));
            }
        } else {
            // Notify specific users provided
            foreach ($users as $user) {
                $user->notify(new ExamNotification($exam, $action));
            }
        }
    }

    /**
     * Send assignment notification
     */
    public static function notifyAssignment($assignment, string $action = 'created', ?Collection $users = null)
    {
        $users = $users ?? User::where('role', 'admin')->get();
        foreach ($users as $user) {
            $user->notify(new AssignmentNotification($assignment, $action));
        }
    }

    /**
     * Send result published notification
     */
    public static function notifyResultPublished($exam, int $totalStudents = 0, ?Collection $users = null)
    {
        $users = $users ?? User::where('role', 'admin')->get();
        foreach ($users as $user) {
            $user->notify(new ResultNotification($exam, $totalStudents));
        }
    }

    /**
     * Send attendance notification
     */
    public static function notifyAttendance($student, array $attendanceData = [], string $action = 'marked', ?Collection $users = null)
    {
        $users = $users ?? User::where('role', 'admin')->get();
        foreach ($users as $user) {
            $user->notify(new AttendanceNotification($student, $attendanceData, $action));
        }
    }

    /**
     * Send student notification
     */
    public static function notifyStudent($student, string $action, array $data = [], ?Collection $users = null)
    {
        $users = $users ?? User::where('role', 'admin')->get();
        foreach ($users as $user) {
            $user->notify(new StudentNotification($student, $action, $data));
        }
    }

    /**
     * Send notification to specific users
     */
    public static function notifyUsers(Collection $users, string $notificationClass, ...$parameters)
    {
        foreach ($users as $user) {
            $user->notify(new $notificationClass(...$parameters));
        }
    }

    /**
     * Send notification to single user
     */
    public static function notifyUser($user, string $notificationClass, ...$parameters)
    {
        $user->notify(new $notificationClass(...$parameters));
    }
}

