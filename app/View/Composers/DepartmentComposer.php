<?php

namespace App\View\Composers;

use App\Models\College;
use App\Models\Department;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DepartmentComposer
{
    public function compose(View $view): void
    {
        $department = null;

        try {
            if (Schema::hasTable('departments')) {
                $department = Department::first();
            } elseif (Schema::hasTable('colleges')) {
                // Backward-compat: before the rename migration runs.
                $department = College::first();
            }
        } catch (\Throwable $e) {
            $department = null;
        }

        $departmentLogoUrl = $department ? $department->getLogoUrl() : asset('images/default-logo.svg');

        // Preferred names
        $view->with('department', $department);
        $view->with('departmentLogoUrl', $departmentLogoUrl);

        // Backwards-compatible names (gradual rename safety)
        $view->with('college', $department);
        $view->with('collegeLogoUrl', $departmentLogoUrl);
    }
}
