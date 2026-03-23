/**
 * Dual Date Picker JavaScript (BS/AD)
 * Supports Bikram Sambat and Gregorian calendars with toggle
 * Initializes in AD mode by default for primary operations
 */

// BS Calendar Data (2070-2090 BS)
const bsCalendarData = {
    // Month lengths for each BS year (approximate)
    // Format: [days in each month]
    2070: [31, 29, 32, 31, 31, 30, 30, 29, 30, 29, 30, 31],
    2071: [31, 29, 32, 31, 31, 30, 30, 29, 30, 29, 30, 31],
    2072: [31, 30, 32, 31, 31, 30, 30, 30, 29, 30, 29, 31],
    2073: [31, 30, 32, 31, 31, 30, 30, 30, 29, 30, 29, 31],
    2074: [31, 30, 32, 32, 31, 30, 30, 29, 30, 29, 30, 31],
    2075: [31, 30, 32, 32, 31, 30, 30, 29, 30, 29, 30, 31],
    2076: [31, 30, 32, 32, 31, 30, 30, 29, 30, 30, 29, 31],
    2077: [31, 30, 32, 32, 31, 31, 29, 30, 30, 29, 30, 30],
    2078: [31, 29, 32, 31, 31, 31, 29, 30, 30, 29, 30, 30],
    2079: [31, 29, 32, 31, 31, 31, 29, 30, 30, 29, 30, 30],
    2080: [31, 30, 32, 31, 31, 30, 30, 29, 30, 29, 30, 31],
    2081: [31, 30, 32, 31, 31, 30, 30, 29, 30, 29, 30, 31],
    2082: [31, 30, 32, 31, 32, 30, 30, 29, 30, 29, 30, 31],
    2083: [31, 30, 32, 32, 31, 30, 30, 30, 29, 30, 29, 31],
    2084: [31, 31, 32, 32, 31, 30, 30, 30, 29, 30, 29, 31],
    2085: [31, 31, 32, 32, 31, 30, 30, 30, 29, 30, 29, 31],
    2086: [31, 31, 32, 32, 31, 31, 29, 30, 30, 29, 30, 30],
    2087: [31, 29, 32, 31, 31, 31, 29, 30, 30, 29, 30, 30],
    2088: [31, 30, 32, 31, 31, 30, 30, 29, 30, 29, 30, 31],
    2089: [31, 30, 32, 31, 31, 30, 30, 29, 30, 29, 30, 31],
    2090: [31, 30, 32, 32, 31, 30, 30, 29, 30, 29, 30, 31]
};

// BS Month names (Devanagari)
const bsMonthNames = [
    'बैशाख', 'जेठ', 'असार', 'श्रावण', 'भाद्र', 'आश्विन',
    'कार्तिक', 'मंसिर', 'पौष', 'माघ', 'फाल्गुन', 'चैत्र'
];

// BS Month names (Latin)
const bsMonthNamesLatin = [
    'Baisakh', 'Jestha', 'Ashar', 'Shrawan', 'Bhadra', 'Ashwin',
    'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'
];

// Nepali numbers for display
const nepaliNumbers = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];

// Day names (Devanagari)
const dayNames = ['आइत', 'सोम', 'मंगल', 'बुध', 'बिही', 'शुक्र', 'शनि'];

// Store picker instances
const pickerInstances = {};

/**
 * Convert English number to Nepali
 */
function toNepaliNumber(num) {
    return String(num).split('').map(digit => {
        return nepaliNumbers[parseInt(digit)] || digit;
    }).join('');
}

/**
 * Convert Nepali number to English
 */
function toEnglishNumber(num) {
    return String(num).split('').map(digit => {
        const index = nepaliNumbers.indexOf(digit);
        return index >= 0 ? index : digit;
    }).join('');
}

/**
 * Parse BS date string (YYYY-MM-DD)
 */
function parseBsDate(dateStr) {
    if (!dateStr || typeof dateStr !== 'string') return null;
    const parts = dateStr.split('-');
    if (parts.length !== 3) return null;
    
    const year = parseInt(parts[0]);
    const month = parseInt(parts[1]);
    const day = parseInt(parts[2]);
    
    if (isNaN(year) || isNaN(month) || isNaN(day)) return null;
    
    return { year, month, day };
}

/**
 * Parse AD date string (YYYY-MM-DD)
 */
function parseAdDate(dateStr) {
    if (!dateStr || typeof dateStr !== 'string') return null;
    const parts = dateStr.split('-');
    if (parts.length !== 3) return null;
    
    const year = parseInt(parts[0]);
    const month = parseInt(parts[1]);
    const day = parseInt(parts[2]);
    
    if (isNaN(year) || isNaN(month) || isNaN(day)) return null;
    
    return { year, month, day };
}

/**
 * Get current BS date
 */
function getCurrentBsDate() {
    const now = new Date();
    // Approximate conversion (BS = AD + 56 years and 8 months)
    let bsYear = now.getFullYear() + 56;
    let bsMonth = now.getMonth() + 1 - 8; // 0-indexed
    if (bsMonth <= 0) {
        bsMonth += 12;
        bsYear -= 1;
    }
    const bsDay = now.getDate();
    
    return { year: bsYear, month: bsMonth, day: bsDay };
}

/**
 * Convert AD to BS (approximate)
 */
function adToBs(adDate) {
    const { year, month, day } = parseAdDate(adDate);
    if (!year) return null;
    
    let bsYear = year + 56;
    let bsMonth = month - 8;
    
    if (bsMonth <= 0) {
        bsMonth += 12;
        bsYear -= 1;
    }
    
    return {
        year: bsYear,
        month: bsMonth,
        day: day
    };
}

/**
 * Convert BS to AD (approximate)
 */
function bsToAd(bsDate) {
    const { year, month, day } = parseBsDate(bsDate);
    if (!year) return null;
    
    let adYear = year - 56;
    let adMonth = month + 8;
    
    if (adMonth > 12) {
        adMonth -= 12;
        adYear += 1;
    }
    
    return {
        year: adYear,
        month: adMonth,
        day: day
    };
}

/**
 * Get days in BS month
 */
function getBsDaysInMonth(year, month) {
    return bsCalendarData[year] ? bsCalendarData[year][month - 1] : 30;
}

/**
 * Format BS date for display
 */
function formatBsDisplay(bsDate) {
    const parsed = parseBsDate(bsDate);
    if (!parsed) return '';
    
    const { year, month, day } = parsed;
    return `${bsMonthNames[month - 1]} ${toNepaliNumber(day)}, ${toNepaliNumber(year)}`;
}

/**
 * Format AD date for display
 */
function formatAdDisplay(adDate) {
    const parsed = parseAdDate(adDate);
    if (!parsed) return '';
    
    const { year, month, day } = parsed;
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return `${monthNames[month - 1]} ${day}, ${year}`;
}

/**
 * Initialize a dual date picker
 */
function initDualDatePicker(id, initialValue, defaultMode) {
    const container = document.querySelector(`[data-picker-id="${id}"]`) || 
                     document.querySelector(`#${id}_ad`)?.closest('.dual-date-picker-container');
    
    if (!container) {
        console.warn(`Date picker container not found for: ${id}`);
        return;
    }
    
    const hiddenInput = document.getElementById(`${id}_ad`);
    const displayEl = document.getElementById(`${id}_bs_display`);
    const calendarEl = document.getElementById(`${id}_calendar`);
    const daysGrid = document.getElementById(`${id}_days`);
    const toggleBtns = container.querySelectorAll('.calendar-toggle-btn');
    
    if (!hiddenInput || !displayEl || !calendarEl || !daysGrid) {
        console.warn(`Date picker elements not found for: ${id}`);
        return;
    }
    
    // Store instance
    pickerInstances[id] = {
        hiddenInput,
        displayEl,
        calendarEl,
        daysGrid,
        container,
        currentBsYear: null,
        currentBsMonth: null,
        currentBsDay: null,
        mode: defaultMode || 'ad',
        selectedAdDate: initialValue || null,
        selectedBsDate: null
    };
    
    // Calculate current BS date
    const currentBs = getCurrentBsDate();
    pickerInstances[id].currentBsYear = currentBs.year;
    pickerInstances[id].currentBsMonth = currentBs.month;
    pickerInstances[id].currentBsDay = currentBs.day;
    
    // If initial value provided, convert to BS
    if (initialValue) {
        const bs = adToBs(initialValue);
        if (bs) {
            pickerInstances[id].selectedAdDate = initialValue;
            pickerInstances[id].selectedBsDate = `${bs.year}-${String(bs.month).padStart(2, '0')}-${String(bs.day).padStart(2, '0')}`;
        }
    }
    
    // Set up toggle buttons
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.dataset.mode;
            if (!mode) return;
            
            // Update buttons
            toggleBtns.forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-gray-200', 'text-gray-700');
            });
            this.classList.remove('bg-gray-200', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white');
            
            pickerInstances[id].mode = mode;
            renderCalendar(id);
        });
        
        // Set initial active state
        const mode = btn.dataset.mode;
        if (mode === (defaultMode || 'ad')) {
            btn.classList.add('bg-blue-600', 'text-white');
            btn.classList.remove('bg-gray-200', 'text-gray-700');
        } else {
            btn.classList.add('bg-gray-200', 'text-gray-700');
            btn.classList.remove('bg-blue-600', 'text-white');
        }
    });
    
    // Render initial calendar
    renderCalendar(id);
    
    // Update display if value exists
    if (initialValue) {
        updateDisplay(id);
    }
}

/**
 * Render calendar for a picker
 */
function renderCalendar(id) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    const { currentBsYear, currentBsMonth, selectedBsDate, mode } = instance;
    
    // Get month/year based on mode
    let displayYear, displayMonth, monthNames;
    
    if (mode === 'ad') {
        // For AD mode, convert current BS to AD for display
        const currentAd = bsToAd({ year: currentBsYear, month: currentBsMonth, day: 1 });
        displayYear = currentAd.year;
        displayMonth = currentAd.month;
        monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                     'July', 'August', 'September', 'October', 'November', 'December'];
    } else {
        displayYear = currentBsYear;
        displayMonth = currentBsMonth;
        monthNames = bsMonthNames;
    }
    
    // Update header
    const titleEl = instance.calendarEl.querySelector('.calendar-title');
    if (titleEl) {
        const bsYear = currentBsYear;
        const bsMonthName = bsMonthNamesLatin[currentBsMonth - 1];
        
        if (mode === 'ad') {
            titleEl.innerHTML = `
                <span class="ad-month-year">${monthNames[displayMonth - 1]} ${displayYear}</span>
                <span class="bs-month-year devanagari-text text-gray-500 text-sm ml-1">(${bsMonthName} ${toNepaliNumber(bsYear)})</span>
            `;
        } else {
            titleEl.innerHTML = `
                <span class="bs-month-year devanagari-text">${monthNames[displayMonth - 1]} ${toNepaliNumber(displayYear)}</span>
                <span class="ad-month-year text-gray-500 text-sm ml-1">(${monthNames[displayMonth - 1]} ${displayYear})</span>
            `;
        }
    }
    
    // Clear days grid
    instance.daysGrid.innerHTML = '';
    
    // Get first day of month
    let firstDay;
    if (mode === 'ad') {
        firstDay = new Date(displayYear, displayMonth - 1, 1).getDay();
    } else {
        // For BS, approximate
        firstDay = new Date(displayYear + 56, displayMonth + 7, 1).getDay();
    }
    
    // Add empty cells for days before first day
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'p-1';
        instance.daysGrid.appendChild(emptyCell);
    }
    
    // Get days in month
    let daysInMonth;
    if (mode === 'ad') {
        daysInMonth = new Date(displayYear, displayMonth, 0).getDate();
    } else {
        daysInMonth = getBsDaysInMonth(displayYear, displayMonth);
    }
    
    // Add day cells
    const today = new Date();
    let todayAd, todayBs;
    
    // Parse selected date
    let selectedDay = null;
    if (selectedBsDate) {
        const parsed = parseBsDate(selectedBsDate);
        if (parsed) {
            // If in AD mode, check AD date
            if (mode === 'ad' && selectedAdDate) {
                const adParsed = parseAdDate(selectedAdDate);
                if (adParsed && adParsed.year === displayYear && adParsed.month === displayMonth) {
                    selectedDay = adParsed.day;
                }
            } else if (mode === 'bs') {
                if (parsed.year === displayYear && parsed.month === displayMonth) {
                    selectedDay = parsed.day;
                }
            }
        }
    }
    
    // Check if today
    const todayAdStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    const todayBsDate = adToBs(todayAdStr);
    const todayBsStr = `${todayBsDate.year}-${String(todayBsDate.month).padStart(2, '0')}-${String(todayBsDate.day).padStart(2, '0')}`;
    
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('button');
        dayCell.type = 'button';
        
        // Determine if this is today
        let isToday = false;
        if (mode === 'ad') {
            isToday = displayYear === today.getFullYear() && 
                      displayMonth === today.getMonth() + 1 && 
                      day === today.getDate();
        } else {
            isToday = displayYear === todayBsDate.year && 
                      displayMonth === todayBsDate.month && 
                      day === todayBsDate.day;
        }
        
        // Determine if selected
        const isSelected = day === selectedDay;
        
        // Build classes
        let classes = ['p-1', 'text-sm', 'rounded', 'transition-colors', 'hover:bg-blue-100'];
        
        if (isToday) {
            classes.push('font-bold', 'border', 'border-blue-500');
        }
        
        if (isSelected) {
            classes.push('bg-blue-600', 'text-white', 'hover:bg-blue-700');
        } else {
            classes.push('text-gray-700');
        }
        
        dayCell.className = classes.join(' ');
        
        // Display number based on mode
        if (mode === 'ad') {
            dayCell.textContent = day;
        } else {
            dayCell.innerHTML = `<span class="devanagari-text">${toNepaliNumber(day)}</span>`;
        }
        
        // Click handler
        dayCell.addEventListener('click', () => selectDate(id, day, displayMonth, displayYear, mode));
        
        instance.daysGrid.appendChild(dayCell);
    }
}

/**
 * Select a date
 */
function selectDate(id, day, month, year, mode) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    let adDate, bsDate;
    
    if (mode === 'ad') {
        adDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const bs = adToBs(adDate);
        if (bs) {
            bsDate = `${bs.year}-${String(bs.month).padStart(2, '0')}-${String(bs.day).padStart(2, '0')}`;
        }
        
        // Update current BS to match
        const currentBs = getCurrentBsDate();
        instance.currentBsYear = currentBs.year;
        instance.currentBsMonth = currentBs.month;
    } else {
        bsDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const ad = bsToAd(bsDate);
        if (ad) {
            adDate = `${ad.year}-${String(ad.month).padStart(2, '0')}-${String(ad.day).padStart(2, '0')}`;
        }
    }
    
    // Update state
    instance.selectedAdDate = adDate;
    instance.selectedBsDate = bsDate;
    
    // Update hidden input
    if (instance.hiddenInput) {
        instance.hiddenInput.value = adDate || '';
    }
    
    // Update display
    updateDisplay(id);
    
    // Close calendar
    if (instance.calendarEl) {
        instance.calendarEl.classList.add('hidden');
    }
}

/**
 * Update display element
 */
function updateDisplay(id) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    const { selectedAdDate, selectedBsDate, displayEl } = instance;
    
    if (!displayEl) return;
    
    const bsTextEl = displayEl.querySelector('.bs-date-text');
    const adPreviewEl = displayEl.querySelector('.ad-date-preview');
    
    if (selectedBsDate && bsTextEl) {
        bsTextEl.textContent = formatBsDisplay(selectedBsDate);
    } else if (selectedAdDate && bsTextEl) {
        // Convert AD to BS for display
        const bs = adToBs(selectedAdDate);
        if (bs) {
            const bsStr = `${bs.year}-${String(bs.month).padStart(2, '0')}-${String(bs.day).padStart(2, '0')}`;
            bsTextEl.textContent = formatBsDisplay(bsStr);
        }
    }
    
    if (adPreviewEl) {
        if (selectedAdDate) {
            adPreviewEl.textContent = `(${formatAdDisplay(selectedAdDate)})`;
        } else if (selectedBsDate) {
            const ad = bsToAd(parseBsDate(selectedBsDate));
            if (ad) {
                const adStr = `${ad.year}-${String(ad.month).padStart(2, '0')}-${String(ad.day).padStart(2, '0')}`;
                adPreviewEl.textContent = `(${formatAdDisplay(adStr)})`;
            }
        }
    }
}

/**
 * Navigate calendar
 */
function changeMonth(id, delta) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    if (instance.mode === 'ad') {
        // Convert current BS to AD, then add delta
        const currentAd = bsToAd({ 
            year: instance.currentBsYear, 
            month: instance.currentBsMonth, 
            day: 1 
        });
        
        let newMonth = currentAd.month + delta;
        let newYear = currentAd.year;
        
        if (newMonth <= 0) {
            newMonth += 12;
            newYear -= 1;
        } else if (newMonth > 12) {
            newMonth -= 12;
            newYear += 1;
        }
        
        // Convert back to BS for storage
        const adStr = `${newYear}-${String(newMonth).padStart(2, '0')}-01`;
        const newBs = adToBs(adStr);
        if (newBs) {
            instance.currentBsYear = newBs.year;
            instance.currentBsMonth = newBs.month;
        }
    } else {
        // BS mode
        instance.currentBsMonth += delta;
        if (instance.currentBsMonth <= 0) {
            instance.currentBsMonth += 12;
            instance.currentBsYear -= 1;
        } else if (instance.currentBsMonth > 12) {
            instance.currentBsMonth -= 12;
            instance.currentBsYear += 1;
        }
    }
    
    renderCalendar(id);
}

/**
 * Select today's date
 */
function selectToday(id) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    const today = new Date();
    const adDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;
    const bs = adToBs(adDate);
    
    if (!bs) return;
    
    const bsDate = `${bs.year}-${String(bs.month).padStart(2, '0')}-${String(bs.day).padStart(2, '0')}`;
    
    // Update state
    instance.selectedAdDate = adDate;
    instance.selectedBsDate = bsDate;
    instance.currentBsYear = bs.year;
    instance.currentBsMonth = bs.month;
    
    // Update hidden input
    if (instance.hiddenInput) {
        instance.hiddenInput.value = adDate;
    }
    
    // Update display
    updateDisplay(id);
    
    // Close calendar
    if (instance.calendarEl) {
        instance.calendarEl.classList.add('hidden');
    }
}

/**
 * Clear selected date
 */
function clearDate(id) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    instance.selectedAdDate = null;
    instance.selectedBsDate = null;
    
    if (instance.hiddenInput) {
        instance.hiddenInput.value = '';
    }
    
    // Reset display
    const displayEl = instance.displayEl;
    if (displayEl) {
        const bsTextEl = displayEl.querySelector('.bs-date-text');
        const adPreviewEl = displayEl.querySelector('.ad-date-preview');
        
        if (bsTextEl) {
            bsTextEl.textContent = 'Select date';
        }
        if (adPreviewEl) {
            adPreviewEl.textContent = '';
        }
    }
}

/**
 * Toggle calendar visibility
 */
function toggleCalendar(id) {
    const instance = pickerInstances[id];
    if (!instance) return;
    
    if (instance.calendarEl) {
        instance.calendarEl.classList.toggle('hidden');
        if (!instance.calendarEl.classList.contains('hidden')) {
            renderCalendar(id);
        }
    }
}

// Close calendars when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.closest('.dual-date-picker-container')) return;
    
    Object.values(pickerInstances).forEach(instance => {
        if (instance.calendarEl && !instance.calendarEl.contains(e.target)) {
            instance.calendarEl.classList.add('hidden');
        }
    });
});

// Expose functions globally
window.initDualDatePicker = initDualDatePicker;
window.changeMonth = changeMonth;
window.selectToday = selectToday;
window.clearDate = clearDate;
window.toggleCalendar = toggleCalendar;
window.formatBsDisplay = formatBsDisplay;
window.formatAdDisplay = formatAdDisplay;

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all date pickers on the page
    document.querySelectorAll('.dual-date-picker-container').forEach(container => {
        const toggleBtn = container.querySelector('.calendar-toggle-btn[data-target]');
        if (toggleBtn) {
            const id = toggleBtn.dataset.target;
            const hiddenInput = document.getElementById(`${id}_ad`);
            const initialValue = hiddenInput ? hiddenInput.value : '';
            const defaultMode = container.dataset.calendarMode || 'ad';
            
            // Small delay to ensure DOM is ready
            setTimeout(() => {
                initDualDatePicker(id, initialValue, defaultMode);
            }, 100);
        }
    });
});

