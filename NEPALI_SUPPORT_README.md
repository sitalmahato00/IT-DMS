# UTF-8 Unicode Nepali Support for Laravel

This document describes the implementation of UTF-8 Unicode Nepali (Devanagari script) support in the IT-DMS Laravel application, following Nepal Government Website Standards.

## Features

- ✅ **utf8mb4** charset for full Unicode support in MySQL
- ✅ **Direct Nepali content storage** - No transliteration, stores Devanagari directly
- ✅ **Bilingual content support** - Separate fields for Nepali and English content
- ✅ **Proper Devanagari fonts** - Noto Sans Devanagari with fallback fonts
- ✅ **Bikram Sambat (BS) date support** - For Nepali calendar dates
- ✅ **Nepali number formatting** - Devanagari numerals (०, १, २, ...)
- ✅ **Blade directives** - Easy-to-use @nepali, @bsDate, etc.

## Installation

### 1. Database Configuration

The database is already configured for `utf8mb4` charset in `config/database.php`:

```php
'mysql' => [
    'charset' => env('DB_CHARSET', 'utf8mb4'),
    'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
    // ...
],
```

For existing MySQL databases, run:

```sql
ALTER DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- For each table
ALTER TABLE your_table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Run Migration

```bash
php artisan migrate
```

This creates tables with proper Nepali support:
- `nepali_notices` - Simple bilingual notices
- `bilingual_notices` - Full-featured bilingual notices
- `study_materials_bilingual` - Bilingual study materials

### 3. Clear Cache

```bash
php artisan optimize:clear
```

## Usage

### Blade Directives

#### Display Nepali Content
```blade
{{-- Simple display --}}
@nepali($notice->content_ne)

{{-- With fallback to English --}}
@nepali($notice->content_ne, $notice->content_en)

{{-- Wrapped in Devanagari font --}}
@devanagari
    यहाँ नेपाली सामग्री लेख्नुहोस्
@enddevanagari
```

#### Get Localized Content
```blade
{{-- Returns Nepali content if locale is ne, otherwise English --}}
@localized($notice->title_ne, $notice->title_en)
```

#### Nepali Numbers
```blade
{{-- Convert English to Nepali --}}
@nepaliNumber(1234567) {{-- १२३४५६७ --}}

{{-- Format with decimals --}}
@nepaliNumber(1234567.89) {{-- १,२३४,५६७.८९ --}}

{{-- Convert Nepali to English --}}
@englishNumber('१२३') {{-- 123 --}}
```

#### Bikram Sambat Dates
```blade
{{-- Format BS date --}}
@bsDate($notice->published_date_bs)

{{-- Custom format --}}
@bsDate($notice->published_date_bs, 'Y-m-d')

{{-- Get month name --}}
@bsMonth(1) {{-- बैशाख --}}

{{-- Get day name --}}
@bsDay(0) {{-- आइतबार --}}
```

#### Utility Directives
```blade
{{-- Format number with Nepali locale --}}
@nepaliFormat(1234567.89)

{{-- Check if text contains Devanagari --}}
@containsdevanagari($text)
    This text has Nepali characters
@endcontainsdevanagari

{{-- Generate URL slug from Nepali --}}
@nepaliSlug($nepaliText) {{-- Returns URL-friendly slug --}}
```

### Using the Helper Class

```php
use App\Helpers\NepaliContentHelper;

// Store Nepali content directly
$content = NepaliContentHelper::storeNepali('यो नेपाली सामग्री हो');

// Display with proper font
echo NepaliContentHelper::displayNepali($content);

// Get content based on locale
echo NepaliContentHelper::getLocalizedContent($model->title_ne, $model->title_en);

// Convert numbers
NepaliContentHelper::toNepaliNumber(123); // १२३
NepaliContentHelper::toEnglishNumber('१२३'); // 123

// Format numbers
NepaliContentHelper::formatNumber(1234567.89); // १,२३४,५६७.८९

// Check for Devanagari
NepaliContentHelper::containsDevanagari('नेपाली'); // true
NepaliContentHelper::containsDevanagari('English'); // false

// Generate slug
NepaliContentHelper::slug('नेपाली सामग्री'); // 'nepali-samagri'
```

### Using the Model

```php
use App\Models\BilingualNotice;

// Get notices with localized content
$notice = BilingualNotice::find(1);

// Automatically returns content based on current locale
echo $notice->localized_title;
echo $notice->localized_content;

// Get labels in current locale
echo $notice->priority_label; // महत्वपूर्ण or Important
echo $notice->audience_label; // विद्यार्थीहरू or Students

// Get formatted date
echo $notice->formatted_published_date;

// Query published notices
$notices = BilingualNotice::published()
    ->important()
    ->forAudience('students')
    ->inCategory('exam')
    ->get();
```

## Database Schema Example

### Bilingual Notices Table

```sql
CREATE TABLE bilingual_notices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title_ne VARCHAR(500) COMMENT 'सूचना शीर्षक (Nepali)',
    title_en VARCHAR(500) COMMENT 'Notice Title (English)',
    content_ne TEXT COMMENT 'सूचना सामग्री (Nepali)',
    content_en TEXT COMMENT 'Notice Content (English)',
    audience ENUM('all', 'students', 'faculty', 'parents') DEFAULT 'all',
    audience_label_ne VARCHAR(50) COMMENT 'दर्शक लेबल (Nepali)',
    category VARCHAR(50) DEFAULT 'general',
    category_label_ne VARCHAR(100) COMMENT 'श्रेणी लेबल (Nepali)',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    priority_label_ne VARCHAR(50) COMMENT 'प्राथमिकता लेबल (Nepali)',
    published_date DATE DEFAULT CURRENT_DATE,
    expiry_date DATE NULL,
    published_date_bs VARCHAR(20) COMMENT 'प्रकाशित मिति (Bikram Sambat)',
    expiry_date_bs VARCHAR(20) COMMENT 'म्याद समाप्ति मिति (Bikram Sambat)',
    is_published BOOLEAN DEFAULT TRUE,
    is_important BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    
    INDEX idx_published_date (is_published, published_date),
    INDEX idx_audience (is_published, audience),
    INDEX idx_important (is_important, is_published)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Font Configuration

The application uses **Noto Sans Devanagari** as the primary font, following Nepal Government standards.

### Font Stack (in order of preference)

1. **Noto Sans Devanagari** - Primary (Google Fonts)
2. **Mangal** - Common Windows Nepali font
3. **Preeti** - Popular Nepali font
4. **Kantipur** - Nepali newspaper font

### CSS Configuration

```css
[lang="ne"] {
    font-family: 'Noto Sans Devanagari', 'Mangal', 'Preeti', 'Kantipur', sans-serif;
}

.devanagari-text {
    font-family: 'Noto Sans Devanagari', 'Mangal', 'Preeti', 'Kantipur', sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}
```

## Best Practices

### 1. Store Content Directly in Unicode

```php
// ✅ Correct - Store Nepali directly
$notice->content_ne = 'यो नेपाली सामग्री हो';

// ❌ Wrong - Don't use transliteration
$notice->content_ne = 'yo nepali samanro ho';
```

### 2. Use Separate Fields for Each Language

```php
// ✅ Correct - Separate fields
$notice->title_ne = 'सूचना शीर्षक';
$notice->title_en = 'Notice Title';

// ❌ Wrong - Don't mix languages in one field
$notice->title_ne = 'Notice Title / सूचना शीर्षक';
```

### 3. Translate Only UI Labels

```php
// ✅ Correct - UI labels in lang files
__('Notice Title') // Returns "सूचना शीर्षक" when locale is ne

// ❌ Wrong - Don't translate content
$notice->title_ne = __('Notice Title'); // Content should be in Nepali
```

### 4. Use Proper Indexes for Search

```php
// For MySQL, add FULLTEXT index for Nepali search
DB::statement('ALTER TABLE notices ADD FULLTEXT INDEX notices_fulltext (title_ne, content_ne)');
```

## File Structure

```
app/
├── Helpers/
│   └── NepaliContentHelper.php    # Utility functions for Nepali content
├── Models/
│   └── BilingualNotice.php        # Model with bilingual support
└── Providers/
    └── NepaliSupportServiceProvider.php  # Blade directives registration

database/migrations/
└── 2026_02_15_utf8mb4_nepali_support.php  # Database tables

resources/
├── css/
│   └── app.css                    # Devanagari font configuration
└── lang/
    ├── en.json                    # English UI labels
    └── ne.json                    # Nepali UI labels
```

## References

- [Nepal Government Portal](https://www.nepal.gov.np)
- [National Language Commission](https://language.gov.np)
- [Unicode Devanagari Range](https://unicode.org/charts/PDF/U0900.pdf)
- [Noto Sans Devanagari](https://fonts.google.com/noto/specimen/Noto+Sans+Devanagari)
- [Bikram Sambat Calendar](https://en.wikipedia.org/wiki/Vikram_Samvat)

## License

This implementation follows standard Laravel licensing and is part of the IT-DMS project.
