<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FeaturesGrid extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'Comprehensive Management Tools',
        public string $subtitle = 'Everything you need to manage your academic institution efficiently'
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.features-grid', [
            'features' => $this->getFeatures(),
        ]);
    }

    /**
     * Get the features data
     */
    public function getFeatures()
    {
        return [
            [
                'id' => 1,
                'title' => 'Attendance Management',
                'description' => 'Track student attendance automatically with real-time updates and reports.',
                'icon' => '📊',
                'color' => 'bg-blue-100'
            ],
            [
                'id' => 2,
                'title' => 'Reports & Grades',
                'description' => 'Generate comprehensive reports and manage student grades effortlessly.',
                'icon' => '📈',
                'color' => 'bg-pink-100'
            ],
            [
                'id' => 3,
                'title' => 'Study Materials',
                'description' => 'Share and organize study materials, notes, and educational resources.',
                'icon' => '📚',
                'color' => 'bg-purple-100'
            ],
            [
                'id' => 4,
                'title' => 'Notices & Announcements',
                'description' => 'Send instant notifications and announcements to students and staff.',
                'icon' => '🔔',
                'color' => 'bg-green-100'
            ],
            [
                'id' => 5,
                'title' => 'Results & Analytics',
                'description' => 'Analyze performance data with powerful analytics and insights.',
                'icon' => '📊',
                'color' => 'bg-orange-100'
            ],
            [
                'id' => 6,
                'title' => 'Seamless Communication',
                'description' => 'Enable smooth communication between teachers, students, and parents.',
                'icon' => '💬',
                'color' => 'bg-red-100'
            ]
        ];
    }
}

