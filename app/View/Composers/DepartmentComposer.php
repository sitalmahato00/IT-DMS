<?php

namespace App\View\Composers;

use App\Models\College;
use App\Models\Department;
use App\Support\SafeCache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DepartmentComposer
{
    public function compose(View $view): void
    {
        $department = once(function () {
            return SafeCache::remember(
                'department:shared-current:v1',
                (int) config('performance.department_cache_ttl', 600),
                function () {
                    try {
                        if (Schema::hasTable('departments')) {
                            return Department::first();
                        }

                        if (Schema::hasTable('colleges')) {
                            // Backward-compat: before the rename migration runs.
                            return College::first();
                        }
                    } catch (\Throwable) {
                        return null;
                    }

                    return null;
                }
            );
        });

        $departmentLogoUrl = $department ? $department->getLogoUrl() : asset('images/default-logo.svg');

        // Preferred names
        $view->with('department', $department);
        $view->with('departmentLogoUrl', $departmentLogoUrl);

        // Backwards-compatible names (gradual rename safety)
        $view->with('college', $department);
        $view->with('collegeLogoUrl', $departmentLogoUrl);
    }
}
