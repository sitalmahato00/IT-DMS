re<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Dual Calendar Date Picker Component
 * 
 * Supports both Bikram Sambat (BS) and Gregorian (AD) calendars.
 * All internal operations use AD dates, but displays BS by default.
 * Hidden input stores AD date for Laravel form submission.
 */
class DualDatePicker extends Component
{
    /**
     * The input name for the date field
     *
     * @var string
     */
    public $name;

    /**
     * The input ID
     *
     * @var string
     */
    public $id;

    /**
     * The label text
     *
     * @var string
     */
    public $label;

    /**
     * The selected date in AD format (YYYY-MM-DD)
     *
     * @var string|null
     */
    public $value;

    /**
     * Whether the field is required
     *
     * @var bool
     */
    public $required;

    /**
     * Help text to display below the field
     *
     * @var string|null
     */
    public $helpText;

    /**
     * Placeholder text
     *
     * @var string
     */
    public $placeholder;

    /**
     * Default calendar mode: 'bs' or 'ad'
     *
     * @var string
     */
    public $defaultMode;

    /**
     * Create a new component instance.
     *
     * @param string $name
     * @param string|null $id
     * @param string|null $label
     * @param string|null $value
     * @param bool $required
     * @param string|null $helpText
     * @param string $placeholder
     * @param string $defaultMode
     */
    public function __construct(
        string $name,
        ?string $id = null,
        ?string $label = null,
        ?string $value = null,
        bool $required = false,
        ?string $helpText = null,
        string $placeholder = 'Select date',
        string $defaultMode = 'bs'
    ) {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label ?? ucfirst(str_replace('_', ' ', $name));
        $this->value = old($name, $value);
        $this->required = $required;
        $this->helpText = $helpText;
        $this->placeholder = $placeholder;
        $this->defaultMode = $defaultMode;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.dual-date-picker');
    }

    /**
     * Get the BS date from AD date
     *
     * @param string|null $adDate
     * @return string|null
     */
    public function getBsDate(?string $adDate): ?string
    {
        if (empty($adDate)) {
            return null;
        }

        // Use the helper class for conversion
        if (class_exists(\App\Helpers\NepaliContentHelper::class)) {
            return \App\Helpers\NepaliContentHelper::convertAdToBs($adDate);
        }

        // Fallback: Simple approximation
        return $this->simpleAdToBs($adDate);
    }

    /**
     * Simple AD to BS conversion (fallback)
     *
     * @param string $adDate
     * @return string|null
     */
    protected function simpleAdToBs(string $adDate): ?string
    {
        $parts = explode('-', $adDate);
        if (count($parts) !== 3) {
            return null;
        }

        $adYear = (int) $parts[0];
        $adMonth = (int) $parts[1];
        $adDay = (int) $parts[2];

        // Approximate conversion: AD + 56-57 years
        $bsYear = $adYear + 56;

        if ($adMonth >= 1 && $adMonth <= 4) {
            $bsMonth = $adMonth + 8;
            $bsYear = $bsYear - 1;
        } else {
            $bsMonth = $adMonth - 8;
        }

        return sprintf('%04d-%02d-%02d', $bsYear, $bsMonth, $adDay);
    }
}

