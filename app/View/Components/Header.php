<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $logoText = 'DMS',
        public string $homeUrl = '/',
        public string $servicesUrl = '#features',
        public string $aboutUrl = '#about',
        public string $contactUrl = '#contact',
        public ?string $loginUrl = null
        // public ?string $getStartedUrl = null,
        // public string $getStartedText = 'Register'
    )
    {
        // Use Laravel route helpers if URLs not provided
        $this->loginUrl = $loginUrl ?? route('login');
        // $this->getStartedUrl = $getStartedUrl ?? route('register');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header');
    }
}
