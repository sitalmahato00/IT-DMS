console.log('app.js loaded');
import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

// JS: Nepali (BS) datepicker integration
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'nepali-date-picker';
import 'nepali-date-picker/dist/nepaliDatePicker.min.css';

function nepaliDigitsToEnglish(str) {
    if (!str) return str;
    const map = { '०':'0','१':'1','२':'2','३':'3','४':'4','५':'5','६':'6','७':'7','८':'8','९':'9' };
    return str.replace(/[०-९]/g, d => map[d] || d);
}

function initBsDatePicker(root = document) {
    const $inputs = $(root).find('input.bs-date').addClass('date-picker');
    try {
        $inputs.each(function() {
            if (!$(this).data('bs-initialized')) {
                $(this).nepaliDatePicker({
                    dateFormat: '%y-%m-%d',
                    closeOnDateSelect: true
                });
                $(this).on('change.bsDatepicker', function() {
                    $(this).val(nepaliDigitsToEnglish($(this).val()));
                });
                $(this).data('bs-initialized', true);
            }
        });
    } catch (e) {
        console.warn('Nepali datepicker init failed:', e);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOMContentLoaded fired, initializing dark mode toggle');
    initBsDatePicker();

    // Dark Mode Toggle Functionality
    const toggleBtn = document.getElementById('darkModeToggle');
    const darkModeIcon = document.getElementById('darkModeIcon');
    const html = document.documentElement;
    
    const moonIcon = 'bi-moon-fill';
    const sunIcon = 'bi-sun-fill';
    
    function getThemePreference() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            return savedTheme;
        }
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }
    
    function applyTheme(theme) {
        if (theme === 'dark') {
            html.classList.add('dark');
            if (darkModeIcon) {
                darkModeIcon.classList.remove(moonIcon);
                darkModeIcon.classList.add(sunIcon);
            }
        } else {
            html.classList.remove('dark');
            if (darkModeIcon) {
                darkModeIcon.classList.remove(sunIcon);
                darkModeIcon.classList.add(moonIcon);
            }
        }
        localStorage.setItem('theme', theme);
    }
    
    function toggleTheme() {
        const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    }
    
    // Initialize theme
    const theme = getThemePreference();
    applyTheme(theme);
    
    // Add toggle event listener
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleTheme);
    }
    
    // Listen for system preference changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }

    // Watch for dynamically added inputs (modals, AJAX content)
    const obs = new MutationObserver((mutations) => {
        mutations.forEach(m => {
            m.addedNodes && m.addedNodes.forEach(node => {
                try {
                    if (node.querySelectorAll && node.querySelectorAll('input.bs-date').length) {
                        initBsDatePicker(node);
                    }
                } catch (e) {}
            });
        });
    });

    obs.observe(document.body, { childList: true, subtree: true });

    // Expose manual initializer if needed
    window.initBsDatePicker = initBsDatePicker;

    // Locale selector: attach JS handler to navigate to locale route (uses data-base-url)
    try {
        const localeSelect = document.getElementById('locale-select');
        if (localeSelect) {
            const base = localeSelect.getAttribute('data-base-url') || '/locale';
            localeSelect.addEventListener('change', function () {
                if (!this.value) return;
                const url = base.replace(/\/$/, '') + '/' + encodeURIComponent(this.value);
                console.log('[Locale] navigating to', url);
                window.location.href = url;
            });
        }
    } catch (e) {
        console.warn('Locale select init failed', e);
    }
});
