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
     * BS Calendar data - Month lengths for each BS year
     * Format: [days in each month (Baisakh to Chaitra)]
     */
    // Calendar data removed: use `anuzpandey/laravel-nepali-date` package instead.

    /**
     * Global base AD offset (in days).
     *
     * If the authoritative calendar requires a fixed shift relative to the
     * base reference (BS 2000-01-01 = AD 1943-04-14), adjust this value.
     * Positive values move the base AD forward (resulting BS dates become
     * earlier), negative values move it backward.
     */
    // Conversions delegated to `anuzpandey/laravel-nepali-date` package.

    /**
     * Convert AD to BS using calendar-based calculation
     * 
     * @param string $adDate The AD date in YYYY-MM-DD format
     * @return string|null The BS date in YYYY-MM-DD format, or null if conversion fails
     */
    public static function adToBs(string $adDate): ?string
    {
        if (empty($adDate)) {
            return null;
        }

        try {
            if (function_exists('toNepaliDate')) {
                return toNepaliDate($adDate, 'Y-m-d', 'en');
            }

            if (class_exists(\Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::class)) {
                return \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($adDate, 'Y-m-d', 'en')->toNepaliDate('Y-m-d', 'en');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Convert AD to BS (alias for adToBs)
     * 
     * @param string $adDate The AD date in YYYY-MM-DD format
     * @return string|null The BS date in YYYY-MM-DD format, or null if conversion fails
     */
    public static function convertAdToBs(string $adDate): ?string
    {
        return self::adToBs($adDate);
    }

    /**
     * Convert BS to AD using calendar-based calculation
     * 
     * @param string $bsDate The BS date in YYYY-MM-DD format
     * @return string|null The AD date in YYYY-MM-DD format, or null if conversion fails
     */
    public static function bsToAd(string $bsDate): ?string
    {
        if (empty($bsDate)) {
            return null;
        }
        try {
            if (function_exists('toEnglishDate')) {
                return toEnglishDate($bsDate, 'Y-m-d', 'en');
            }

            if (class_exists(\Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::class)) {
                return \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($bsDate, 'Y-m-d', 'en')->toEnglishDate('Y-m-d', 'en');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Convert BS to AD (alias for bsToAd)
     * 
     * @param string $bsDate The BS date in YYYY-MM-DD format
     * @return string|null The AD date in YYYY-MM-DD format, or null if conversion fails
     */
    public static function convertBsToAd(string $bsDate): ?string
    {
        return self::bsToAd($bsDate);
    }

    /**
     * Format a BS date into a pretty display string.
     *
     * Examples:
     *  - formatBsPretty('2082-11-02', false) => '02 माघ 2082'
     *
     * @param string $bsDate YYYY-MM-DD
     * @param bool $shortUnused Reserved for compatibility (not used)
     * @return string|null
     */
    public static function formatBsPretty(string $bsDate, bool $shortUnused = false): ?string
    {
        if (empty($bsDate)) return null;

        $parts = explode('-', $bsDate);
        if (count($parts) < 3) return null;

        [$y, $m, $d] = $parts;
        $m = (int) $m;
        $d = str_pad((int)$d, 2, '0', STR_PAD_LEFT);

        $neMonths = [
            1 => 'बैशाख',
            2 => 'जेठ',
            3 => 'असार',
            4 => 'साउन',
            5 => 'भदौ',
            6 => 'असोज',
            7 => 'कार्तिक',
            8 => 'मंसिर',
            9 => 'पुष',
            10 => 'माघ',
            11 => 'फागुन',
            12 => 'चैत्र',
        ];

        $monthName = $neMonths[$m] ?? '';
        return trim($d . ' ' . $monthName . ' ' . $y);
    }

    /**
     * Get localized content based on current app locale.
     *
     * @param string|null $nepali
     * @param string|null $english
     * @param string|null $fallback
     * @return string|null
     */
    public static function getLocalizedContent(?string $nepali, ?string $english, ?string $fallback = null): ?string
    {
        $locale = app()->getLocale();

        if ($locale === 'ne') {
            if (!empty($nepali)) {
                return $nepali;
            }
            if (!empty($english)) {
                return $english;
            }
        }

        if (!empty($english)) {
            return $english;
        }

        if (!empty($nepali)) {
            return $nepali;
        }

        return $fallback;
    }

    /**
     * Display Nepali text if locale is ne, otherwise fallback text.
     *
     * @param string|null $text
     * @param string|null $fallback
     * @return string
     */
    public static function displayNepali(?string $text, ?string $fallback = null): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ne' && !empty($text)) {
            return $text;
        }

        return $fallback ?? $text ?? '';
    }

    /**
     * Convert English numerals to Nepali numerals.
     *
     * @param int|string $value
     * @return string
     */
    public static function toNepaliNumber($value): string
    {
        $map = ['0' => '०', '1' => '१', '2' => '२', '3' => '३', '4' => '४', '5' => '५', '6' => '६', '7' => '७', '8' => '८', '9' => '९'];
        return preg_replace_callback('/\d/', function ($matches) use ($map) {
            return $map[$matches[0]];
        }, (string) $value);
    }

    /**
     * Convert Nepali digits to English digits.
     *
     * @param string $value
     * @return string
     */
    public static function toEnglishNumber(string $value): string
    {
        $map = ['०' => '0', '१' => '1', '२' => '2', '३' => '3', '४' => '4', '५' => '5', '६' => '6', '७' => '7', '८' => '8', '९' => '9'];
        return str_replace(array_keys($map), array_values($map), $value);
    }

    /**
     * Format number with Nepali numerals if locale is ne.
     *
     * @param int|float|string $number
     * @return string
     */
    public static function formatNumber($number): string
    {
        $locale = app()->getLocale();
        if ($locale !== 'ne') {
            return (string) $number;
        }

        return self::toNepaliNumber($number);
    }

    /**
     * Format a BS date using helper package or fallback value.
     *
     * @param string|null $date
     * @param string $format
     * @return string|null
     */
    public static function formatBsDate(?string $date, string $format = 'Y-m-d'): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (class_exists(\Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::class)) {
            try {
                return \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from($date, 'Y-m-d', 'en')->toNepaliDate($format, 'en');
            } catch (\Throwable $e) {
                return $date;
            }
        }

        return $date;
    }

    /**
     * Format a BS date into a Latin-transliterated pretty string.
     *
     * Examples:
     *  - formatBsPrettyLatin('2082-01-01', true) => 'baisakh 01, 2082'
     *
     * @param string $bsDate YYYY-MM-DD
     * @param bool $lowercase Whether to return month name in lowercase (default true)
     * @return string|null
     */
    public static function formatBsPrettyLatin(string $bsDate, bool $lowercase = true): ?string
    {
        if (empty($bsDate)) return null;

        $parts = explode('-', $bsDate);
        if (count($parts) < 3) return null;

        [$y, $m, $d] = $parts;
        $m = (int) $m;
        $d = str_pad((int)$d, 2, '0', STR_PAD_LEFT);

        $latinMonths = [
            1 => 'Baisakh',
            2 => 'Jestha',
            3 => 'Asar',
            4 => 'Saun',
            5 => 'Bhadra',
            6 => 'Asoj',
            7 => 'Kartik',
            8 => 'Mansir',
            9 => 'Poush',
            10 => 'Magh',
            11 => 'Fagun',
            12 => 'Chaitra',
        ];

        $monthName = $latinMonths[$m] ?? '';
        if ($lowercase) {
            $monthName = mb_strtolower($monthName);
        }

        return trim($monthName . ' ' . $d . ', ' . $y);
    }
}

