<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentNoticeController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $locale = app()->getLocale();
        $audience = (string) $request->get('audience', 'all');
        $query = trim((string) $request->get('q', ''));
        $subjectIds = $student->subjects()->pluck('subjects.id')->all();

        $noticeQuery = Notice::published()
            ->with('creator', 'subject')
            ->where(function ($builder) use ($subjectIds) {
                $builder->whereNull('subject_id');
                if (!empty($subjectIds)) {
                    $builder->orWhereIn('subject_id', $subjectIds);
                }
            })
            ->where(function ($builder) {
                $builder->where('audience', 'all')
                    ->orWhere('audience', 'students');
            })
            ->orderBy('is_important', 'desc')
            ->orderBy('published_at_bs', 'desc')
            ->orderBy('created_at', 'desc');

        if ($audience !== '' && $audience !== 'all') {
            if ($audience === 'important') {
                $noticeQuery->where('is_important', true);
            } else {
                $noticeQuery->where(function ($builder) use ($audience) {
                    $builder->where('audience', $audience)
                        ->orWhere('audience', 'all');
                });
            }
        }

        if ($query !== '') {
            $noticeQuery->where(function ($builder) use ($query) {
                $builder->where('title', 'like', '%' . $query . '%')
                    ->orWhere('title_ne', 'like', '%' . $query . '%')
                    ->orWhere('message', 'like', '%' . $query . '%')
                    ->orWhere('message_ne', 'like', '%' . $query . '%');
            });
        }

        $notices = $noticeQuery->paginate(9)->withQueryString();

        $noticeStats = [
            'total' => Notice::published()->count(),
            'important' => Notice::published()->where('is_important', true)->count(),
            'students' => Notice::published()->where(function ($builder) {
                $builder->where('audience', 'students')->orWhere('audience', 'all');
            })->count(),
        ];

        $audienceOptions = [
            'all' => $locale === 'ne' ? 'सबै' : 'All',
            'important' => $locale === 'ne' ? 'महत्वपूर्ण' : 'Important',
            'students' => $locale === 'ne' ? 'विद्यार्थीहरू' : 'Students',
        ];

        return view('student.notices.index', compact(
            'notices',
            'audience',
            'query',
            'audienceOptions',
            'noticeStats'
        ));
    }

    public function show($id)
    {
        $student = Auth::user()?->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $subjectIds = $student->subjects()->pluck('subjects.id')->all();

        $notice = Notice::published()
            ->with('creator', 'subject')
            ->where(function ($builder) use ($subjectIds) {
                $builder->whereNull('subject_id');
                if (!empty($subjectIds)) {
                    $builder->orWhereIn('subject_id', $subjectIds);
                }
            })
            ->where(function ($builder) {
                $builder->where('audience', 'all')
                    ->orWhere('audience', 'students');
            })
            ->findOrFail($id);

        return view('student.notices.show', compact('notice'));
    }
}

