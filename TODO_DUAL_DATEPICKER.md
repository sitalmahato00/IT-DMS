# Dual Date Picker (BS/AD) Implementation

## Plan Status: APPROVED

### Files to Create:

1. [x] `app/View/Components/DualDatePicker.php` - PHP Component class
2. [x] `resources/views/components/dual-date-picker.blade.php` - Blade View  
3. [x] `resources/js/dual-date-picker.js` - JavaScript with BS calendar data

### Implementation Steps:

- [ ] Create PHP Component Class
- [ ] Create Blade View Component
- [ ] Create JavaScript with Full BS Calendar Data
- [ ] Update app.js to include the new script
- [ ] Test component integration

## Key Features:
- Toggle between Bikram Sambat (BS) and Gregorian (AD) calendars
- Accurate BS calendar data (2070-2090 BS range)
- Hidden input stores AD date (YYYY-MM-DD) for Laravel forms
- Display shows BS date in Nepali format by default
- BS ↔ AD conversion on toggle
- Nepali numbers (Devanagari) for all BS dates
- TailwindCSS styling matching project design
- Bootstrap Icons integration

