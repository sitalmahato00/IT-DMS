<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Support\ParentPortalData;
use Illuminate\Http\Request;

class ParentExportController extends Controller
{
    public function __construct(
        private readonly ParentPortalData $portalData
    ) {
    }

    /**
     * Export a parent summary report to CSV.
     */
    public function export(Request $request)
    {
        $portal = $this->portalData->build($request->user());
        $filename = 'parent_portal_export_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($portal) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Parent Portal Summary']);
            fputcsv($handle, ['Parent Name', $portal['parentUser']->name ?? '']);
            fputcsv($handle, ['Parent Email', $portal['parentUser']->email ?? '']);
            fputcsv($handle, ['Phone', $portal['parentUser']->phone ?? optional($portal['parentProfile'])->phone ?? '']);
            fputcsv($handle, ['Children Count', $portal['childrenCount']]);
            fputcsv($handle, ['Overall Attendance', $portal['overallAttendance'] . '%']);
            fputcsv($handle, ['Overall Score', $portal['overallScore'] !== null ? $portal['overallScore'] . '%' : 'Pending']);
            fputcsv($handle, ['Unread Notifications', $portal['unreadNotificationCount']]);
            fputcsv($handle, []);

            fputcsv($handle, ['Children Overview']);
            fputcsv($handle, [
                'Student Name',
                'Roll No',
                'Semester',
                'Academic Year',
                'Attendance %',
                'Overall Score %',
                'CGPA',
                'Passed Subjects',
                'Failed Subjects',
                'Pending Subjects',
            ]);

            foreach ($portal['children'] as $child) {
                fputcsv($handle, [
                    $child['name'],
                    $child['roll_no'],
                    $child['semester'],
                    $child['academic_year'],
                    $child['attendance_percentage'],
                    $child['overall_percentage'] ?? 'Pending',
                    $child['cgpa'] ?? 'Pending',
                    $child['passed_subjects'],
                    $child['failed_subjects'],
                    $child['pending_subjects'],
                ]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Subject Performance']);
            fputcsv($handle, [
                'Student Name',
                'Subject',
                'Code',
                'Teacher',
                'Attendance %',
                'Obtained Marks',
                'Full Marks',
                'Percentage',
                'Status',
                'Next Exam',
            ]);

            foreach ($portal['children'] as $child) {
                foreach ($child['subjects'] as $subject) {
                    fputcsv($handle, [
                        $child['name'],
                        $subject['name'],
                        $subject['code'],
                        $subject['teacher_name'],
                        $subject['attendance_percentage'],
                        $subject['obtained_marks'] ?? 'Pending',
                        $subject['full_marks'] ?? 'Pending',
                        $subject['percentage'] ?? 'Pending',
                        $subject['status_label'],
                        $subject['next_exam']['date_label'] ?? 'Not scheduled',
                    ]);
                }
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Recent Notices']);
            fputcsv($handle, ['Title', 'Audience', 'Priority', 'Date']);

            foreach ($portal['recentNotices'] as $notice) {
                fputcsv($handle, [
                    $notice->localized_title,
                    $notice->localized_audience_label,
                    $notice->localized_priority_label,
                    $notice->formatted_date,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
