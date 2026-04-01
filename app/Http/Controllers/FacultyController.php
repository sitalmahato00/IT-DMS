<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Teacher;
use App\Models\User;
use App\Support\SafeCache;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $ttl = (int) config('performance.public_data_cache_ttl', 300);
        $departmentTtl = (int) config('performance.department_cache_ttl', 600);

        $department = SafeCache::remember('faculty:department:v1', $departmentTtl, fn () => Department::first());

        $departmentKey = $department?->short_name ?: $department?->name;

        $teachers = SafeCache::remember('faculty:teachers:' . md5((string) $departmentKey) . ':v1', $ttl, function () use ($departmentKey) {
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
                return Teacher::with(['user'])
                    ->where('status', 'active')
                    ->get();
            }

            return $teachers;
        });

        $teachers = $teachers
            ->sortBy(fn ($t) => $t->user?->name ?: '')
            ->values();

        $hods = SafeCache::remember('faculty:admins:v1', $ttl, function () {
            return User::where('role', 'admin')
                ->orderBy('name')
                ->get();
        });

        return view('faculty.index', compact('department', 'teachers', 'hods'));
    }
}
