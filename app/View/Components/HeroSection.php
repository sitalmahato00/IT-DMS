<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeroSection extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'Modern Academic Management Made Simple',
        public string $description = 'Comprehensive academic management system designed to simplify your educational institution\'s daily operations.',
        public string $primaryBtnText = 'Get Started Now',
        public ?string $primaryBtnUrl = null,
        public string $secondaryBtnText = 'Discover',
        public ?string $secondaryBtnUrl = null,
        public string $imageSrc = '/images/hero-image.jpg'
    )
    {
        // Use Laravel route helpers if URLs not provided
        // $this->primaryBtnUrl = $primaryBtnUrl ?? route('register');
        $this->primaryBtnUrl = $primaryBtnUrl ?? route('login');
        $this->secondaryBtnUrl = $secondaryBtnUrl ?? '#features';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.hero-section');
    }
}

