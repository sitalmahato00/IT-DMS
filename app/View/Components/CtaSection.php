<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CtaSection extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'Ready to Transform Your Department?',
        public string $description = 'Join hundreds of departments already using DMS to streamline their academic operations and enhance student success.',
        public string $primaryBtnText = 'Start Free Trial',
        public ?string $primaryBtnUrl = null,
        public string $secondaryBtnText = 'Schedule Demo',
        public ?string $secondaryBtnUrl = null
    )
    {
        // Use Laravel route helpers if URLs not provided
        // $this->primaryBtnUrl = $primaryBtnUrl ?? route('register');
        $this->primaryBtnUrl = $primaryBtnUrl ?? route('login');
        $this->secondaryBtnUrl = $secondaryBtnUrl ?? '#contact';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cta-section');
    }
}

