<?php

namespace App\Providers;

use App\Helpers\NepaliContentHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class NepaliSupportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Nepali content Blade directives
        
        /**
         * Display Nepali content with proper font styling
         * 
         * Usage:
         * @nepali($content)
         * @nepali($content, $fallback)
         */
        Blade::directive('nepali', function ($expression) {
            return "<?php echo NepaliContentHelper::displayNepali{$expression}; ?>";
        });

        /**
         * Get localized content based on current locale
         * 
         * Usage:
         * @localized($neField, $enField)
         */
        Blade::directive('localized', function ($expression) {
            return "<?php echo NepaliContentHelper::getLocalizedContent{$expression}; ?>";
        });

        /**
         * Convert number to Nepali (Devanagari)
         * 
         * Usage:
         * @nepaliNumber(123)
         */
        Blade::directive('nepaliNumber', function ($expression) {
            return "<?php echo NepaliContentHelper::toNepaliNumber{$expression}; ?>";
        });

        /**
         * Convert Nepali number to English
         * 
         * Usage:
         * @englishNumber('१२३')
         */
        Blade::directive('englishNumber', function ($expression) {
            return "<?php echo NepaliContentHelper::toEnglishNumber{$expression}; ?>";
        });

        /**
         * Display formatted Nepali date (Bikram Sambat)
         * 
         * Usage:
         * @bsDate($date)
         * @bsDate($date, 'Y-m-d')
         */
        Blade::directive('bsDate', function ($expression) {
            return "<?php echo NepaliContentHelper::formatBsDate{$expression}; ?>";
        });

        /**
         * Get Nepali month name
         * 
         * Usage:
         * @bsMonth(1) // Returns 'बैशाख'
         */
        Blade::directive('bsMonth', function ($expression) {
            return "<?php echo NepaliContentHelper::getMonthName{$expression}; ?>";
        });

        /**
         * Get Nepali day name
         * 
         * Usage:
         * @bsDay(0) // Returns 'आइतबार'
         */
        Blade::directive('bsDay', function ($expression) {
            return "<?php echo NepaliContentHelper::getDayName{$expression}; ?>";
        });

        /**
         * Format number with Nepali locale
         * 
         * Usage:
         * @nepaliFormat(1234567.89)
         */
        Blade::directive('nepaliFormat', function ($expression) {
            return "<?php echo NepaliContentHelper::formatNumber{$expression}; ?>";
        });

        /**
         * Check if text contains Devanagari characters
         * 
         * Usage:
         * @ifcontainsdevanagari($text)
         */
        Blade::directive('containsdevanagari', function ($expression) {
            return "<?php if (NepaliContentHelper::containsDevanagari{$expression}): ?>";
        });

        /**
         * Generate Nepali slug for URL
         * 
         * Usage:
         * @nepaliSlug($text)
         */
        Blade::directive('nepaliSlug', function ($expression) {
            return "<?php echo NepaliContentHelper::slug{$expression}; ?>";
        });

        /**
         * Apply Devanagari font class
         * 
         * Usage:
         * @devanagari
         *     यहाँ नेपाली सामग्री लेख्नुहोस्
         * @enddevanagari
         */
        Blade::directive('devanagari', function () {
            return '<span class="devanagari-text nepali-content">';
        });

        Blade::directive('enddevanagari', function () {
            return '</span>';
        });
    }
}
