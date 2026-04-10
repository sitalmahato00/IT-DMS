<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Support\ParentPortalData;
use App\Support\PublicMarksheetBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ParentPortalController extends Controller
{
    public function __construct(
        private readonly ParentPortalData $portalData
    ) {
    }

    public function dashboard(Request $request)
    {
        return view('parent.parentdashboard', $this->buildViewData($request));
    }

    public function children(Request $request)
    {
        return view('parent.children', $this->buildViewData($request));
    }

    public function attendance(Request $request)
    {
        return view('parent.attendance', $this->buildViewData($request));
    }

    public function results(Request $request)
    {
        return view('parent.results', $this->buildViewData($request));
    }

    public function exams(Request $request)
    {
        $viewData = $this->buildViewData($request);
        $selectedChild = $viewData['selectedChild'] ?? null;

        $examGroups = collect($selectedChild['recent_results'] ?? [])
            ->filter(fn (array $entry) => !empty($entry['exam_id']))
            ->groupBy('exam_id')
            ->map(function (Collection $entries, $examId) {
                $entries = $entries->sortByDesc(fn (array $entry) => $entry['sort_key'] ?? 0)->values();
                $first = $entries->first();
                $obtained = $entries->sum('obtained_marks');
                $full = $entries->sum('full_marks');
                $percentage = $full > 0 ? round(($obtained / $full) * 100, 2) : null;
                $hasFail = $entries->contains(fn (array $entry) => ($entry['status'] ?? '') === 'fail');
                $isPending = $entries->contains(fn (array $entry) => ($entry['status'] ?? '') === 'pending');

                return [
                    'exam_id' => (int) $examId,
                    'label' => $first['label'] ?? __('Exam'),
                    'category' => $first['category'] ?? __('Exam'),
                    'type' => $first['type'] ?? __('Exam'),
                    'date_label' => $first['date_label'] ?? __('Date pending'),
                    'subject_count' => $entries->count(),
                    'obtained_marks' => $obtained,
                    'full_marks' => $full,
                    'percentage' => $percentage,
                    'status' => $isPending ? 'pending' : ($hasFail ? 'fail' : 'pass'),
                    'status_label' => $isPending ? __('Pending') : ($hasFail ? __('Needs attention') : __('Pass')),
                    'entries' => $entries,
                ];
            })
            ->sortByDesc(function (array $group) {
                $firstDate = $group['entries']->first()['date'] ?? null;
                return $firstDate instanceof \Carbon\Carbon ? $firstDate->timestamp : 0;
            })
            ->values();

        return view('parent.exams', array_merge($viewData, [
            'examGroups' => $examGroups,
        ]));
    }

    public function courses(Request $request)
    {
        return view('parent.courses', $this->buildViewData($request));
    }

    public function notices(Request $request)
    {
        return view('parent.notices', $this->buildViewData($request));
    }

    public function communication(Request $request)
    {
        return view('parent.communication', $this->buildViewData($request));
    }

    public function events(Request $request)
    {
        return view('parent.events', $this->buildViewData($request));
    }

    public function print(Request $request)
    {
        return view('parent.print', $this->buildViewData($request));
    }

    public function examsPrint(Request $request)
    {
        $viewData = $this->buildViewData($request);
        $selectedChild = $viewData['selectedChild'] ?? null;

        if (!$selectedChild) {
            return redirect()->route('parent.exams', array_filter(['child' => $viewData['selectedChildId']]))
                ->with('error', 'No child is selected for this marksheet.');
        }

        $student = Student::with('user')->find($selectedChild['id'] ?? null);

        if (!$student) {
            return redirect()->route('parent.exams', array_filter(['child' => $viewData['selectedChildId']]))
                ->with('error', 'Exam marksheet not found.');
        }

        $payload = app(PublicMarksheetBuilder::class)->build($student, $request->integer('exam_id') ?: null);

        return view('admin.marks.marksheet-print', $payload);
    }

    private function buildViewData(Request $request): array
    {
        $requestedChildId = $request->integer('child');

        $data = $this->portalData->build($request->user(), [
            'selected_child_id' => $requestedChildId,
            'section' => $request->query('section'),
        ]);

        $selectedChild = $this->resolveSelectedChild($data['children'], $requestedChildId);

        return array_merge($data, [
            'selectedChild' => $selectedChild,
            'selectedChildId' => $selectedChild['id'] ?? null,
        ]);
    }

    private function resolveSelectedChild(Collection $children, ?int $selectedChildId): ?array
    {
        if ($children->isEmpty()) {
            return null;
        }

        if ($selectedChildId) {
            $selected = $children->firstWhere('id', $selectedChildId);

            if ($selected) {
                return $selected;
            }
        }

        return $children->first();
    }
}

