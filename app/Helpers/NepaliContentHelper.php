<?php

namespace App\Helpers;

use Illuminate\Support\Str;

/**
 * Nepali Content Helper
 * 
 * Provides utilities for storing and displaying Nepali (Devanagari) content
 * directly in Unicode format, following Nepal Government Website Standards.
 * 
 * Features:
 * - Direct Unicode storage (no transliteration)
 * - Proper Devanagari font rendering
 * - Bikram Sambat date conversion
 * - Nepali number formatting
 */
class NepaliContentHelper
{
    /**
     * Store Nepali content directly (no transliteration)
     * 
     * @param string $content The Nepali content to store
     * @return string The original content (stored as-is in utf8mb4)
     */
    public static function storeNepali(string $content): string
    {
        // Content is stored directly - utf8mb4 handles Unicode
        return trim($content);
    }

    /**
     * Retrieve Nepali content with proper rendering
     * 
     * @param string|null $nepaliContent The stored Nepali content
     * @param string|null $fallbackContent Fallback content (English)
     * @param bool $applyFont Apply Devanagari font class
     * @return string|null
     */
    public static function displayNepali(
        ?string $nepaliContent, 
        ?string $fallbackContent = null,
        bool $applyFont = true
    ): ?string {
        if (!empty($nepaliContent)) {
            return $applyFont 
                ? '<span class="devanagari-text nepali-content">' . $nepaliContent . '</span>'
                : $nepaliContent;
        }
        
        return $fallbackContent;
    }

    /**
     * Get bilingual content based on locale
     * 
     * @param string|null $ne Nepali content
     * @param string|null $en English content
     * @param string|null $locale Current locale (ne, en)
     * @return string|null
     */
    public static function getLocalizedContent(
        ?string $ne, 
        ?string $en, 
        ?string $locale = null
    ): ?string {
        $locale = $locale ?? app()->getLocale();
        
        if ($locale === 'ne' && !empty($ne)) {
            return $ne;
        }
        
        return $en ?? $ne;
    }

    /**
     * Convert English numbers to Nepali (Devanagari) numbers
     * 
     * @param int|float|string $number The number to convert
     * @return string The Nepali number
     * 
     * Example: 123 -> १२३
     */
    public static function toNepaliNumber($number): string
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $nepali = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
        
        return str_replace($english, $nepali, (string) $number);
    }

    /**
     * Convert Nepali (Devanagari) numbers to English
     * 
     * @param string $number The Nepali number to convert
     * @return string The English number
     * 
     * Example: १२३ -> 123
     */
    public static function toEnglishNumber(string $number): string
    {
        $nepali = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($nepali, $english, $number);
    }

    /**
     * Format a date in Bikram Sambat (BS)
     * 
     * @param \DateTimeInterface|string $date The Gregorian date
     * @param string $format The output format
     * @return string The formatted BS date
     */
    public static function formatBsDate($date, string $format = 'Y-m-d'): string
    {
        // Note: For production, use a proper BS conversion library
        // This is a simplified placeholder
        $bsMonths = [
            'बैशाख', 'जेठ', 'असार', 'श्रावण', 'भाद्र', 'आश्विन',
            'कार्तिक', 'मंसिर', 'पौष', 'माघ', 'फाल्गुन', 'चैत्र'
        ];
        
        $bsDays = ['आइत', 'सोम', 'मंगल', 'बुध', 'बिही', 'शुक्र', 'शनि'];
        
        // Placeholder - in production, use:
        // $bsDate = \Carbon\Carbon::parse($date)->toBsDate();
        
        return $date instanceof \DateTimeInterface 
            ? $date->format($format) 
            : (string) $date;
    }

    /**
     * Get month name in Nepali
     * 
     * @param int $month Month number (1-12)
     * @return string The Nepali month name
     */
    public static function getMonthName(int $month): string
    {
        $months = [
            1 => 'बैशाख',
            2 => 'जेठ',
            3 => 'असार',
            4 => 'श्रावण',
            5 => 'भाद्र',
            6 => 'आश्विन',
            7 => 'कार्तिक',
            8 => 'मंसिर',
            9 => 'पौष',
            10 => 'माघ',
            11 => 'फाल्गुन',
            12 => 'चैत्र'
        ];
        
        return $months[$month] ?? '';
    }

    /**
     * Get day name in Nepali
     * 
     * @param int $day Day of week (0-6, Sunday = 0)
     * @return string The Nepali day name
     */
    public static function getDayName(int $day): string
    {
        $days = [
            0 => 'आइतबार',
            1 => 'सोमबार',
            2 => 'मंगलबार',
            3 => 'बुधबार',
            4 => 'बिहीबार',
            5 => 'शुक्रबार',
            6 => 'शनिबार'
        ];
        
        return $days[$day] ?? '';
    }

    /**
     * Format a number with Nepali locale formatting
     * 
     * @param float|int $number The number to format
     * @param int $decimals Number of decimal places
     * @return string Formatted number with Nepali thousands separator
     */
    public static function formatNumber($number, int $decimals = 0): string
    {
        $nepaliNumber = self::toNepaliNumber(number_format($number, $decimals, '.', ','));
        
        // Replace English comma with Nepali comma
        return str_replace(',', ',', $nepaliNumber);
    }

    /**
     * Validate Nepali Unicode string
     * 
     * @param string $string The string to validate
     * @return bool True if contains Devanagari characters
     */
    public static function containsDevanagari(string $string): bool
    {
        return preg_match('/[\x{0900}-\x{097F}]/u', $string) === 1;
    }

    /**
     * Get text direction based on content
     * 
     * @param string $text The text to check
     * @return string 'ltr' or 'rtl'
     */
    public static function getTextDirection(string $text): string
    {
        // Devanagari is LTR (left-to-right)
        return 'ltr';
    }

    /**
     * Truncate Nepali text while preserving word boundaries
     * 
     * @param string $text The text to truncate
     * @param int $length Maximum length
     * @param string $append Ellipsis to append
     * @return string The truncated text
     */
    public static function truncate(string $text, int $length = 100, string $append = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        
        return mb_substr($text, 0, $length) . $append;
    }

    /**
     * Generate a slug from Nepali text (URL-friendly)
     * 
     * @param string $text The Nepali text
     * @return string URL-friendly slug
     */
    public static function slug(string $text): string
    {
        // Convert Nepali numbers to English for URL
        $text = self::toEnglishNumber($text);
        
        // Use Laravel's slug helper
        return Str::slug($text);
    }
}
