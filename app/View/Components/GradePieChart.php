<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Grade Distribution Pie Chart Component
 * 
 * Displays grade distribution (A, B, C, D, F) as a pie chart.
 * Supports both passed grade data and real data from the database.
 */
class GradePieChart extends Component
{
    /**
     * The grade distribution data
     *
     * @var array
     */
    public $gradeDistribution;

    /**
     * Chart ID for unique identification
     *
     * @var string
     */
    public $chartId;

    /**
     * Whether to show labels
     *
     * @var bool
     */
    public $showLabels;

    /**
     * Create a new component instance.
     *
     * @param array $gradeDistribution
     * @param string|null $chartId
     * @param bool $showLabels
     */
    public function __construct(
        array $gradeDistribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
        ?string $chartId = null,
        bool $showLabels = true
    ) {
        $this->gradeDistribution = $gradeDistribution;
        $this->chartId = $chartId ?? 'gradePieChart';
        $this->showLabels = $showLabels;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.grade-pie-chart');
    }

    /**
     * Get total grades count
     *
     * @return int
     */
    public function getTotalAttribute(): int
    {
        return array_sum($this->gradeDistribution);
    }

    /**
     * Get color for each grade
     *
     * @param string $grade
     * @return string
     */
    public function getGradeColor(string $grade): string
    {
        return match($grade) {
            'A' => '#22c55e', // Green
            'B' => '#3b82f6', // Blue
            'C' => '#f59e0b', // Yellow/Orange
            'D' => '#f97316', // Orange
            'E' => '#ef4444', // Red
            'F' => '#dc2626', // Dark Red
            default => '#6b7280', // Gray
        };
    }
}


