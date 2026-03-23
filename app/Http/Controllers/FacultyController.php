<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $department = Department::first();

        $departmentKey = $department?->short_name ?: $department?->name;

        $teachersQuery = Teacher::with(['user'])
            ->where('status', 'active');

        if (!empty($departmentKey)) {
            $teachersQuery->where(function ($q) use ($departmentKey) {
                $q->where('department', $departmentKey)
                    ->orWhereHas('user', fn ($uq) => $uq->where('department', $departmentKey));
            });
        }

        $teachers = $teachersQuery->get();

        if ($teachers->isEmpty()) {
            $teachers = Teacher::with(['user'])
                ->where('status', 'active')
                ->get();
        }

        $teachers = $teachers
            ->sortBy(fn ($t) => $t->user?->name ?: '')
            ->values();

        $hods = User::where('role', 'admin')
            ->orderBy('name')
            ->get();

        return view('faculty.index', compact('department', 'teachers', 'hods'));
    }
}

