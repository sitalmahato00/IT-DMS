<?php

namespace App\View\Composers;

use App\Models\College;
use Illuminate\View\View;

class CollegeComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $college = College::first();
        
        $view->with('college', $college);
        $view->with('collegeLogoUrl', $college ? $college->getLogoUrl() : asset('images/default-logo.svg'));
    }
}
