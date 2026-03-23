<?php

namespace App\View\Components\Mail;

use Illuminate\View\Component;

class Layout extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('emails.layouts.main');
    }
}

