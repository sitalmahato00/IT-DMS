<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PersonasSection extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'Built for Everyone',
        public string $subtitle = 'Designed to serve all stakeholders in your educational institution'
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.personas-section', [
            'personas' => $this->getPersonas(),
        ]);
    }

    /**
     * Get the personas data
     */
    public function getPersonas()
    {
        return [
            [
                'id' => 1,
                'title' => 'Teachers',
                'description' => 'Manage classes, track attendance, grade assignments, and communicate with students and parents efficiently.',
                'icon' => '👨‍🏫',
                'benefits' => [
                    'Attendance tracking',
                    'Grade management',
                    'Class scheduling',
                    'Student communication'
                ],
                'color' => 'bg-blue-500'
            ],
            [
                'id' => 2,
                'title' => 'Students',
                'description' => 'Access course materials, submit assignments, view grades, and stay updated with announcements.',
                'icon' => '👨‍🎓',
                'benefits' => [
                    'Access study materials',
                    'Submit assignments',
                    'View grades & results',
                    'Get notifications'
                ],
                'color' => 'bg-green-500'
            ],
            [
                'id' => 3,
                'title' => 'Parents',
                'description' => 'Monitor your child\'s academic progress, attendance, and communicate with teachers.',
                'icon' => '👨‍👩‍👧',
                'benefits' => [
                    'Monitor progress',
                    'Check attendance',
                    'View grades',
                    'Contact teachers'
                ],
                'color' => 'bg-purple-500'
            ],
            [
                'id' => 4,
                'title' => 'Administrators',
                'description' => 'Manage the entire institution, generate reports, and oversee all academic and administrative operations.',
                'icon' => '👨‍💼',
                'benefits' => [
                    'System management',
                    'Generate reports',
                    'User management',
                    'Analytics & insights'
                ],
                'color' => 'bg-orange-500'
            ]
        ];
    }
}
