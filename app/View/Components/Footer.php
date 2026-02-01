<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Footer extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $companyName = 'DMS',
        public string $companyDescription = 'Document Management System - Empowering educational institutions with modern management solutions.',
        public string $year = '2024'
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.footer', [
            'footerLinks' => $this->getFooterLinks(),
            'socialLinks' => $this->getSocialLinks(),
        ]);
    }

    /**
     * Get footer links data
     */
    public function getFooterLinks()
    {
        return [
            'Product' => [
                ['label' => 'Features', 'url' => '#features'],
                ['label' => 'Pricing', 'url' => '#pricing'],
                ['label' => 'Security', 'url' => '#security'],
                ['label' => 'Roadmap', 'url' => '#roadmap'],
            ],
            'Resources' => [
                ['label' => 'Documentation', 'url' => '#docs'],
                ['label' => 'Help Center', 'url' => '#help'],
                ['label' => 'Blog', 'url' => '#blog'],
                ['label' => 'Community', 'url' => '#community'],
            ],
            'Contact Us' => [
                ['label' => 'Springfield College', 'url' => '#'],
                ['label' => 'DIT Education Lane', 'url' => '#'],
                ['label' => '+1 (888) 123-4567', 'url' => 'tel:+18881234567'],
                ['label' => 'info@dms.edu', 'url' => 'mailto:info@dms.edu'],
            ],
        ];
    }

    /**
     * Get social media links
     */
    public function getSocialLinks()
    {
        return [
            ['icon' => 'facebook', 'url' => '#facebook', 'label' => 'Facebook'],
            ['icon' => 'twitter', 'url' => '#twitter', 'label' => 'Twitter'],
            ['icon' => 'instagram', 'url' => '#instagram', 'label' => 'Instagram'],
        ];
    }
}
