<?php

namespace App\Support;

use App\Models\Attendance;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\Student;
use App\Models\TimetableSlot;
use App\Models\User;
use App\Helpers\NepaliContentHelper;
use App\Traits\BuildsRoutineTimetable;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ParentPortalData
{
    use BuildsRoutineTimetable;

    public function build(User $user, array $options = []): array
    {
        $user->loadMissing('parent');

        $students = $this->getChildren($user);
        $requestedChildId = isset($options['selected_child_id']) ? (int) $options['selected_child_id'] : null;
        $effectiveSelectedChildId = $requestedChildId ?: $students->first()?->id;
        $requestedSection = trim((string) ($options['section'] ?? ''));

        $children = $students
            ->map(fn (Student $student) => $this->buildChildSnapshot(
                $student,
                $student->id === $effectiveSelectedChildId ? $requestedSection : null
            ))
            ->values();

        $recentNotices = $this->getRelevantNotices($children);

        $upcomingExamFeed = $children
            ->flatMap(function (array $child) {
                return $child['upcoming_exams']->map(function (array $exam) use ($child) {
                    return array_merge($exam, [
                        'child_id' => $child['id'],
                        'child_name' => $child['name'],
                    ]);
                });
            })
            ->sortBy('sort_date')
            ->values();

        $academicAlerts = $children
            ->flatMap(fn (array $child) => $child['alerts'])
            ->sortByDesc('priority')
            ->take(10)
            ->values();

        $teacherContacts = $children
            ->flatMap(fn (array $child) => $child['teachers'])
            ->filter(fn (array $teacher) => !empty($teacher['email']) || !empty($teacher['phone']))
            ->unique(fn (array $teacher) => $teacher['email'] ?: 'teacher-' . $teacher['id'])
            ->values();

        $overallAttendance = $children->isNotEmpty()
            ? round($children->avg('attendance_percentage') ?? 0, 1)
            : 0;

        $overallScore = $children
            ->filter(fn (array $child) => $child['overall_percentage'] !== null)
            ->avg('overall_percentage');

        $cgpaAverage = $children
            ->filter(fn (array $child) => $child['cgpa'] !== null)
            ->avg('cgpa');

        $recentNoticeCount = $recentNotices
            ->filter(function (Notice $notice) {
                $referenceDate = $notice->published_at ?? $notice->created_at;

                return $referenceDate && $referenceDate->greaterThanOrEqualTo(now()->subDays(14));
            })
            ->count();

        return [
            'parentUser' => $user,
            'parentProfile' => $user->parent,
            'children' => $children,
            'primaryChild' => $children->first(),
            'childrenCount' => $children->count(),
            'overallAttendance' => $overallAttendance,
            'overallScore' => $overallScore !== null ? round($overallScore, 2) : null,
            'cgpaAverage' => $cgpaAverage !== null ? round($cgpaAverage, 2) : null,
            'totalSubjects' => $children->sum('subject_count'),
            'totalPassedSubjects' => $children->sum('passed_subjects'),
            'totalFailedSubjects' => $children->sum('failed_subjects'),
            'totalPendingSubjects' => $children->sum('pending_subjects'),
            'unreadNotificationCount' => $user->unreadNotifications()->count(),
            'recentNotices' => $recentNotices,
            'importantNoticeCount' => $recentNotices->where('is_important', true)->count(),
            'recentNoticeCount' => $recentNoticeCount,
            'upcomingExamCount' => $upcomingExamFeed->count(),
            'upcomingExamFeed' => $upcomingExamFeed,
            'academicAlerts' => $academicAlerts,
            'teacherContacts' => $teacherContacts,
        ];
    }

    private function getChildren(User $user): Collection
    {
        $parentIds = collect([$user->id, optional($user->parent)->id])
            ->filter()
            ->unique()
            ->values();

        if ($parentIds->isEmpty()) {
            return collect();
        }

        return Student::query()
            ->whereIn('parent_id', $parentIds)
            ->with([
                'user',
                'subjects.teacherAssignments.teacher.user',
            ])
            ->orderBy('semester')
            ->orderBy('roll_no')
            ->get()
            ->unique('id')
            ->values();
    }

    private function buildChildSnapshot(Student $student, ?string $selectedSection = null): array
    {
        $student->loadMissing([
            'user',
            'subjects.teacherAssignments.teacher.user',
        ]);

        $subjects = $student->subjects
            ->sortBy(fn ($subject) => sprintf('%02d_%s', (int) ($subject->semester ?: 0), strtolower($subject->subject_name ?? '')))
            ->values();

        $attendanceRecords = Attendance::query()
            ->where('student_id', $student->id)
            ->where('attendance_type', 'class')
            ->with('subject')
            ->orderByDesc('date')
            ->get();

        $todayAd = Carbon::now('Asia/Kathmandu')->toDateString();
        $todayBs = NepaliContentHelper::convertAdToBs($todayAd);

        $todayAttendance = Attendance::query()
            ->where('student_id', $student->id)
            ->where('attendance_type', 'class')
            ->where(function ($query) use ($todayAd, $todayBs) {
                $query->whereDate('date', $todayAd)
                    ->orWhere('date_bs', $todayBs);
            })
            ->with('subject')
            ->orderByDesc('date')
            ->get()
            ->map(function (Attendance $record) use ($todayBs) {
                $recordDate = $record->date instanceof Carbon
                    ? $record->date
                    : ($record->date ? Carbon::parse($record->date) : null);

                return [
                    'subject_name' => $record->subject?->subject_name ?? ($record->subject ?? __('Subject')),
                    'subject_code' => $record->subject?->subject_code,
                    'status' => $record->status,
                    'remarks' => $record->remarks,
                    'date' => $recordDate,
                    'date_label' => $recordDate ? $recordDate->format('M d, Y') : __('Date pending'),
                    'date_bs' => $record->date_bs ?? $todayBs,
                ];
            })
            ->values();

        $examMarks = ExamMark::query()
            ->where('student_id', $student->id)
            ->whereHas('exam', function ($query) {
                $query->where('status', Exam::STATUS_PUBLISHED);
                $query->whereIn('exam_category', ['assessment', 'ctevt']);
            })
            ->with(['exam', 'subject'])
            ->get();

        $legacyMarks = Mark::query()
            ->where('student_id', $student->id)
            ->with('subject')
            ->get();

        $upcomingExams = collect();

        if ($subjects->isNotEmpty()) {
            $upcomingExams = Exam::query()
                ->published()
                ->whereIn('subject_id', $subjects->pluck('id'))
                ->when($student->semester, function ($query) use ($student) {
                    $query->where(function ($semesterQuery) use ($student) {
                        $semesterQuery
                            ->whereNull('semester')
                            ->orWhere('semester', '')
                            ->orWhere('semester', (string) $student->semester);
                    });
                })
                ->whereDate('exam_date', '>=', now()->toDateString())
                ->with('subject')
                ->orderBy('exam_date')
                ->get();
        }

        $upcomingExamsBySubject = $upcomingExams
            ->groupBy('subject_id')
            ->map(fn (Collection $group) => $group->sortBy(fn (Exam $exam) => optional($exam->exam_date)?->timestamp ?? PHP_INT_MAX)->first());

        $attendanceBySubject = $attendanceRecords
            ->groupBy('subject_id')
            ->map(function (Collection $records) {
                $totalClasses = $records->count();
                $presentClasses = $records->where('status', 'present')->count();
                $absentClasses = $records->where('status', 'absent')->count();

                return [
                    'total' => $totalClasses,
                    'present' => $presentClasses,
                    'absent' => $absentClasses,
                    'percentage' => $totalClasses > 0 ? round(($presentClasses / $totalClasses) * 100, 1) : 100,
                    'last_record' => $records->first(),
                ];
            });

        $subjectStats = $subjects
            ->map(function ($subject) use ($attendanceBySubject, $examMarks, $legacyMarks, $upcomingExamsBySubject) {
                $subjectExamMarks = $examMarks
                    ->where('subject_id', $subject->id)
                    ->values();

                $subjectLegacyMarks = $legacyMarks
                    ->where('subject_id', $subject->id)
                    ->values();

                $attendanceStats = $attendanceBySubject->get($subject->id, [
                    'total' => 0,
                    'present' => 0,
                    'absent' => 0,
                    'percentage' => 100,
                    'last_record' => null,
                ]);

                $performance = $this->buildSubjectPerformance(
                    $subject,
                    $subjectExamMarks,
                    $subjectLegacyMarks,
                    $attendanceStats,
                    $upcomingExamsBySubject->get($subject->id)
                );

                return array_merge($performance, [
                    'subject_id' => $subject->id,
                ]);
            })
            ->values();

        $gradedSubjects = $subjectStats->filter(fn (array $subject) => $subject['percentage'] !== null);
        $totalObtained = $gradedSubjects->sum('obtained_marks');
        $totalFull = $gradedSubjects->sum('full_marks');
        $overallPercentage = $totalFull > 0 ? round(($totalObtained / $totalFull) * 100, 2) : null;
        $cgpa = $overallPercentage !== null ? round($overallPercentage / 25, 2) : null;

        $recentAttendance = $attendanceRecords
            ->take(8)
            ->map(function (Attendance $record) {
                $recordDate = $record->date instanceof Carbon ? $record->date : ($record->date ? Carbon::parse($record->date) : null);

                return [
                    'subject_name' => $record->subject?->subject_name ?? ($record->subject ?? __('Subject')),
                    'subject_code' => $record->subject?->subject_code,
                    'status' => $record->status,
                    'remarks' => $record->remarks,
                    'date' => $recordDate,
                    'date_label' => $recordDate ? $recordDate->format('M d, Y') : __('Date pending'),
                ];
            })
            ->values();

        $recentResults = $subjectStats
            ->flatMap(fn (array $subject) => $subject['exam_entries']->map(fn (array $entry) => array_merge($entry, [
                'subject_name' => $subject['name'],
                'subject_code' => $subject['code'],
            ])))
            ->sortByDesc(fn (array $entry) => $entry['sort_key'])
            ->take(8)
            ->values();

        $timetableDays = TimetableSlot::getDaysOfWeek();
        $semesterValue = $student->semester ? (string) $student->semester : '';
        $scheduleSections = TimetableSlot::query()
            ->active()
            ->when($semesterValue !== '', fn ($query) => $query->where('semester', $semesterValue))
            ->whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->filter()
            ->map(fn ($section) => (string) $section)
            ->sort()
            ->unique()
            ->values();
        $displaySection = trim((string) ($selectedSection ?? ''));

        if ($displaySection === '' && $scheduleSections->count() === 1) {
            $displaySection = (string) $scheduleSections->first();
        }

        if ($displaySection !== '' && !$scheduleSections->contains($displaySection)) {
            $displaySection = $scheduleSections->count() === 1
                ? (string) $scheduleSections->first()
                : '';
        }

        $timetableSlots = TimetableSlot::query()
            ->active()
            ->with(['subject', 'teacher.user'])
            ->when($semesterValue !== '', fn ($query) => $query->where('semester', $semesterValue))
            ->when($displaySection !== '', fn ($query) => $query->where('section', $displaySection))
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $timetableByDay = $this->buildTimetableByDay($timetableSlots, $timetableDays);
        $timeRows = $this->buildRoutineTimeRows($timetableSlots);
        $slotMatrix = $this->buildRoutineSlotMatrix($timetableDays, $timetableByDay, $timeRows);
        $gapOverrideMatrix = $this->buildRoutineGapOverrideMatrix(
            $semesterValue,
            $displaySection
        );

        $scheduleByDay = collect($timetableDays)
            ->mapWithKeys(function (string $day) use ($timetableByDay) {
                return [$day => collect($timetableByDay[$day] ?? [])
                    ->map(function (TimetableSlot $slot) {
                        return [
                            'subject_name' => $slot->subject?->subject_name ?? __('Class'),
                            'subject_code' => $slot->subject?->subject_code,
                            'teacher_name' => $slot->teacher?->user?->name ?? __('TBA'),
                            'time_range' => $slot->time_range,
                            'short_time_range' => $slot->short_time_range,
                            'room' => $slot->room,
                            'slot_type' => $slot->slot_type,
                        ];
                    })
                    ->values()];
            });

        $teachers = $subjectStats
            ->filter(fn (array $subject) => !empty($subject['teacher_name']))
            ->map(function (array $subject) {
                return [
                    'id' => $subject['teacher_id'],
                    'name' => $subject['teacher_name'],
                    'email' => $subject['teacher_email'],
                    'phone' => $subject['teacher_phone'],
                    'subject_name' => $subject['name'],
                ];
            })
            ->unique(fn (array $teacher) => $teacher['email'] ?: 'teacher-' . $teacher['id'])
            ->values();

        $alerts = $this->buildChildAlerts(
            $student,
            $subjectStats,
            $attendanceRecords,
            $overallPercentage
        );

        return [
            'id' => $student->id,
            'user_id' => $student->user_id,
            'name' => $student->user?->name ?? __('Student'),
            'email' => $student->user?->email,
            'phone' => $student->phone ?: $student->user?->phone,
            'roll_no' => $student->roll_no,
            'semester' => $student->semester,
            'academic_year' => $student->academic_year ?: $student->academic_year_bs,
            'department' => $student->department,
            'gender' => $student->gender,
            'address' => $student->address,
            'status' => $student->status,
            'profile_photo_url' => $student->user?->profile_photo_url,
            'attendance_percentage' => $attendanceRecords->count() > 0
                ? round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100, 1)
                : 100,
            'overall_percentage' => $overallPercentage,
            'cgpa' => $cgpa,
            'subject_count' => $subjectStats->count(),
            'passed_subjects' => $subjectStats->where('status', 'pass')->count(),
            'failed_subjects' => $subjectStats->where('status', 'fail')->count(),
            'pending_subjects' => $subjectStats->where('status', 'pending')->count(),
            'subjects' => $subjectStats,
            'recent_attendance' => $recentAttendance,
            'today_attendance' => $todayAttendance,
            'today_attendance_count' => $todayAttendance->count(),
            'today_attendance_date_label' => Carbon::now('Asia/Kathmandu')->format('M d, Y'),
            'recent_results' => $recentResults,
            'upcoming_exams' => $upcomingExams
                ->map(fn (Exam $exam) => $this->formatUpcomingExam($exam))
                ->values(),
            'schedule_by_day' => $scheduleByDay,
            'timetable_days' => $timetableDays,
            'timetable_by_day' => $timetableByDay,
            'time_rows' => $timeRows,
            'slot_matrix' => $slotMatrix,
            'gap_override_matrix' => $gapOverrideMatrix,
            'timetable_total_slots' => $timetableSlots->count(),
            'timetable_active_days' => collect($timetableByDay)
                ->filter(fn ($slots) => collect($slots)->isNotEmpty())
                ->count(),
            'display_section' => $displaySection,
            'schedule_sections' => $scheduleSections,
            'teachers' => $teachers,
            'alerts' => $alerts,
        ];
    }

    private function buildSubjectPerformance($subject, Collection $examMarks, Collection $legacyMarks, array $attendanceStats, ?Exam $upcomingExam): array
    {
        $teacherAssignment = $subject->teacherAssignments->firstWhere('role', 'primary')
            ?? $subject->teacherAssignments->first();
        $teacherUser = $teacherAssignment?->teacher?->user;

        $assessmentMarks = $examMarks
            ->filter(fn (ExamMark $mark) => $mark->exam?->exam_category === 'assessment' && !$mark->isAbsent())
            ->values();

        $ctevtMark = $examMarks
            ->filter(fn (ExamMark $mark) => $mark->exam?->exam_category === 'ctevt')
            ->sortByDesc(fn (ExamMark $mark) => $this->resolveSortKey($mark->exam?->exam_date, $mark->updated_at))
            ->first();

        $generalExamMark = $examMarks
            ->filter(fn (ExamMark $mark) => !in_array($mark->exam?->exam_category, ['assessment', 'ctevt'], true))
            ->sortByDesc(fn (ExamMark $mark) => $this->resolveSortKey($mark->exam?->exam_date, $mark->updated_at))
            ->first();

        $fullMarks = null;
        $obtainedMarks = null;
        $percentage = null;
        $status = 'pending';
        $grade = null;
        $publishedLabel = __('Awaiting publication');
        $resultType = 'pending';

        if ($assessmentMarks->isNotEmpty()) {
            $fullMarks = round($assessmentMarks->sum(fn (ExamMark $mark) => (float) $mark->effective_full_marks), 2);
            $obtainedMarks = round($assessmentMarks->sum(fn (ExamMark $mark) => (float) $mark->effective_obtained_marks), 2);
            $percentage = $this->calculatePercentage($obtainedMarks, $fullMarks);
            $status = $percentage !== null && $percentage >= 40 ? 'pass' : 'fail';
            $grade = $this->gradeFromPercentage($percentage);
            $publishedLabel = __('Assessment results published');
            $resultType = 'assessment';
        } elseif ($ctevtMark) {
            $fullMarks = round((float) $ctevtMark->effective_full_marks, 2);
            $obtainedMarks = round((float) $ctevtMark->effective_obtained_marks, 2);
            $percentage = $this->calculatePercentage($obtainedMarks, $fullMarks);
            $status = $ctevtMark->isPassedAllComponents() ? 'pass' : 'fail';
            $grade = $ctevtMark->calculateGrade();
            $publishedLabel = __('CTEVT results published');
            $resultType = 'ctevt';
        } elseif ($generalExamMark) {
            $fullMarks = round((float) $generalExamMark->effective_full_marks, 2);
            $obtainedMarks = round((float) $generalExamMark->effective_obtained_marks, 2);
            $percentage = $this->calculatePercentage($obtainedMarks, $fullMarks);
            $status = $percentage !== null && $percentage >= 40 ? 'pass' : 'fail';
            $grade = $generalExamMark->calculateGrade();
            $publishedLabel = __('Exam results published');
            $resultType = 'general';
        } elseif ($legacyMarks->isNotEmpty()) {
            $fullMarks = round((float) $legacyMarks->sum('full_marks'), 2);
            $obtainedMarks = round((float) $legacyMarks->sum('marks_obtained'), 2);
            $percentage = $this->calculatePercentage($obtainedMarks, $fullMarks);
            $status = $percentage !== null && $percentage >= 40 ? 'pass' : 'fail';
            $grade = $this->gradeFromPercentage($percentage);
            $publishedLabel = __('Marks published');
            $resultType = 'legacy';
        }

        return [
            'id' => $subject->id,
            'name' => $subject->subject_name,
            'code' => $subject->subject_code,
            'semester' => $subject->semester,
            'course' => $subject->category ?: $subject->subject_name,
            'credits' => $subject->credits,
            'has_lab' => (bool) $subject->has_lab,
            'description' => $subject->description,
            'teacher_id' => $teacherUser?->id,
            'teacher_name' => $teacherUser?->name ?? __('TBA'),
            'teacher_email' => $teacherUser?->email,
            'teacher_phone' => $teacherAssignment?->teacher?->phone ?: $teacherUser?->phone,
            'attendance_percentage' => $attendanceStats['percentage'],
            'attendance_total' => $attendanceStats['total'],
            'attendance_present' => $attendanceStats['present'],
            'attendance_absent' => $attendanceStats['absent'],
            'full_marks' => $fullMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
            'status' => $status,
            'status_label' => match ($status) {
                'pass' => __('Pass'),
                'fail' => __('Needs attention'),
                default => __('Pending'),
            },
            'grade' => $grade,
            'published_label' => $publishedLabel,
            'result_type' => $resultType,
            'next_exam' => $upcomingExam ? $this->formatUpcomingExam($upcomingExam) : null,
            'exam_entries' => $this->buildExamEntries($examMarks, $legacyMarks),
        ];
    }

    private function buildExamEntries(Collection $examMarks, Collection $legacyMarks): Collection
    {
        $examEntries = $examMarks
            ->map(function (ExamMark $mark) {
                $examDate = $mark->exam?->exam_date;
                $resultStatus = $mark->isAbsent()
                    ? 'absent'
                    : ($mark->isPassedAllComponents() ? 'pass' : 'fail');

                return [
                    'exam_id' => $mark->exam?->id,
                    'label' => $mark->exam?->exam_name ?? __('Exam'),
                    'category' => $mark->exam?->formatted_category ?? __('Exam'),
                    'type' => $mark->exam?->formatted_type ?? __('Assessment'),
                    'subject_id' => $mark->subject?->id,
                    'date' => $examDate,
                    'date_label' => $examDate ? $examDate->format('M d, Y') : __('Date pending'),
                    'obtained_marks' => round((float) $mark->effective_obtained_marks, 2),
                    'full_marks' => round((float) $mark->effective_full_marks, 2),
                    'percentage' => round($mark->calculatePercentage(), 2),
                    'status' => $resultStatus,
                    'status_label' => match ($resultStatus) {
                        'pass' => __('Pass'),
                        'fail' => __('Fail'),
                        default => __('Absent'),
                    },
                    'remarks' => $mark->remarks,
                    'sort_key' => $this->resolveSortKey($examDate, $mark->updated_at),
                ];
            });

        $legacyEntries = $legacyMarks
            ->map(function (Mark $mark) {
                $markDate = $mark->date instanceof Carbon ? $mark->date : ($mark->date ? Carbon::parse($mark->date) : null);
                $percentage = $this->calculatePercentage((float) $mark->marks_obtained, (float) $mark->full_marks);
                $status = $percentage !== null && $percentage >= 40 ? 'pass' : 'fail';

                return [
                    'label' => ucfirst(str_replace('_', ' ', $mark->exam_type ?? __('Mark'))),
                    'category' => __('Recorded Mark'),
                    'type' => ucfirst(str_replace('_', ' ', $mark->exam_type ?? __('Mark'))),
                    'date' => $markDate,
                    'date_label' => $markDate ? $markDate->format('M d, Y') : __('Date pending'),
                    'obtained_marks' => round((float) $mark->marks_obtained, 2),
                    'full_marks' => round((float) $mark->full_marks, 2),
                    'percentage' => $percentage,
                    'status' => $status,
                    'status_label' => $status === 'pass' ? __('Pass') : __('Fail'),
                    'remarks' => null,
                    'sort_key' => $this->resolveSortKey($markDate, $mark->updated_at),
                ];
            });

        return $examEntries
            ->concat($legacyEntries)
            ->sortByDesc('sort_key')
            ->values();
    }

    private function buildChildAlerts(Student $student, Collection $subjects, Collection $attendanceRecords, ?float $overallPercentage): Collection
    {
        $alerts = collect();

        $overallAttendance = $attendanceRecords->count() > 0
            ? round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100, 1)
            : 100;

        if ($overallAttendance < 75) {
            $alerts->push([
                'priority' => 2,
                'tone' => 'warning',
                'child_id' => $student->id,
                'child_name' => $student->user?->name ?? __('Student'),
                'title' => __('Attendance attention'),
                'message' => __(':name is below the recommended 75% attendance target.', [
                    'name' => $student->user?->name ?? __('This student'),
                ]),
            ]);
        }

        if ($overallPercentage !== null && $overallPercentage < 40) {
            $alerts->push([
                'priority' => 3,
                'tone' => 'error',
                'child_id' => $student->id,
                'child_name' => $student->user?->name ?? __('Student'),
                'title' => __('Results need attention'),
                'message' => __(':name currently has an overall score below the passing benchmark.', [
                    'name' => $student->user?->name ?? __('This student'),
                ]),
            ]);
        }

        foreach ($subjects as $subject) {
            if ($subject['attendance_percentage'] < 75) {
                $alerts->push([
                    'priority' => 2,
                    'tone' => 'warning',
                    'child_id' => $student->id,
                    'child_name' => $student->user?->name ?? __('Student'),
                    'title' => __('Subject attendance'),
                    'message' => __(':subject attendance has slipped to :attendance%.', [
                        'subject' => $subject['name'],
                        'attendance' => $subject['attendance_percentage'],
                    ]),
                ]);
            }

            if ($subject['status'] === 'fail') {
                $alerts->push([
                    'priority' => 3,
                    'tone' => 'error',
                    'child_id' => $student->id,
                    'child_name' => $student->user?->name ?? __('Student'),
                    'title' => __('Result follow-up'),
                    'message' => __(':subject currently needs academic support.', [
                        'subject' => $subject['name'],
                    ]),
                ]);
            }

            if ($subject['next_exam'] && ($subject['next_exam']['days_until'] ?? 99) <= 7) {
                $alerts->push([
                    'priority' => 1,
                    'tone' => 'info',
                    'child_id' => $student->id,
                    'child_name' => $student->user?->name ?? __('Student'),
                    'title' => __('Upcoming exam'),
                    'message' => __(':subject has an upcoming exam on :date.', [
                        'subject' => $subject['name'],
                        'date' => $subject['next_exam']['date_label'],
                    ]),
                ]);
            }
        }

        return $alerts
            ->unique(fn (array $alert) => $alert['child_id'] . '|' . $alert['title'] . '|' . $alert['message'])
            ->take(6)
            ->values();
    }

    private function getRelevantNotices(Collection $children): Collection
    {
        $semesterValues = $children
            ->pluck('semester')
            ->filter()
            ->map(fn ($semester) => (string) $semester)
            ->unique()
            ->values();

        return Notice::query()
            ->published()
            ->forAudience('parents')
            ->when($semesterValues->isNotEmpty(), function ($query) use ($semesterValues) {
                $query->where(function ($semesterQuery) use ($semesterValues) {
                    $semesterQuery
                        ->whereNull('semester')
                        ->orWhere('semester', '');

                    foreach ($semesterValues as $semester) {
                        $semesterQuery->orWhere('semester', $semester);
                    }
                });
            })
            ->orderByDesc('is_important')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
    }

    private function formatUpcomingExam(Exam $exam): array
    {
        $examDate = $exam->exam_date instanceof Carbon ? $exam->exam_date : ($exam->exam_date ? Carbon::parse($exam->exam_date) : null);
        $daysUntil = $examDate ? now()->startOfDay()->diffInDays($examDate->startOfDay(), false) : null;

        return [
            'id' => $exam->id,
            'subject_id' => $exam->subject_id,
            'subject_name' => $exam->subject?->subject_name ?? __('Subject'),
            'subject_code' => $exam->subject?->subject_code,
            'exam_name' => $exam->exam_name,
            'category_label' => $exam->formatted_category,
            'type_label' => $exam->formatted_type,
            'date' => $examDate,
            'date_label' => $examDate ? $examDate->format('M d, Y') : __('Date pending'),
            'days_until' => $daysUntil,
            'countdown_label' => match (true) {
                $daysUntil === null => __('Date pending'),
                $daysUntil < 0 => __('Completed'),
                $daysUntil === 0 => __('Today'),
                $daysUntil === 1 => __('Tomorrow'),
                default => __('In :days days', ['days' => $daysUntil]),
            },
            'sort_date' => $examDate ? $examDate->format('Y-m-d H:i:s') : '9999-12-31 23:59:59',
        ];
    }

    private function calculatePercentage(?float $obtained, ?float $full): ?float
    {
        if ($obtained === null || $full === null || $full <= 0) {
            return null;
        }

        return round(($obtained / $full) * 100, 2);
    }

    private function gradeFromPercentage(?float $percentage): ?string
    {
        if ($percentage === null) {
            return null;
        }

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B+',
            $percentage >= 60 => 'B',
            $percentage >= 50 => 'C+',
            $percentage >= 40 => 'C',
            $percentage >= 35 => 'D',
            default => 'F',
        };
    }

    private function resolveSortKey($primaryDate, $secondaryDate): string
    {
        $date = $primaryDate instanceof Carbon
            ? $primaryDate
            : ($primaryDate ? Carbon::parse($primaryDate) : null);

        $fallback = $secondaryDate instanceof Carbon
            ? $secondaryDate
            : ($secondaryDate ? Carbon::parse($secondaryDate) : null);

        return ($date ?? $fallback ?? now()->subYears(100))->format('Y-m-d H:i:s');
    }
}

