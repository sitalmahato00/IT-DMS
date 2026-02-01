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
    initBsDatePicker();

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

    /* Dark mode toggle: persist and apply theme */
    function applyTheme(theme) {
        const checkbox = document.getElementById('darkModeCheckbox');
        const label = document.getElementById('darkModeLabel');

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            if (checkbox) checkbox.checked = true;
            if (label) label.textContent = 'ON';
        } else {
            document.documentElement.classList.remove('dark');
            if (checkbox) checkbox.checked = false;
            if (label) label.textContent = 'OFF';
        }
        try { localStorage.setItem('theme', theme); } catch(e){}
        console.info('[Theme] applied:', theme);
    }

    // Initialize theme from localStorage or prefers-color-scheme. If user sets a preference we respect it; else follow system and listen for changes.
    const saved = (() => { try { return localStorage.getItem('theme'); } catch(e){return null;} })();
    if (saved) applyTheme(saved);
    else {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) applyTheme('dark');
        if (window.matchMedia) {
            const mq = window.matchMedia('(prefers-color-scheme: dark)');
            mq.addEventListener && mq.addEventListener('change', e => {
                const savedNow = (() => { try { return localStorage.getItem('theme'); } catch(e){return null;} })();
                if (!savedNow) applyTheme(e.matches ? 'dark' : 'light');
            });
        }
    }

    // Checkbox-based toggle handler (peer checkbox)
    const checkbox = document.getElementById('darkModeCheckbox');
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            applyTheme(this.checked ? 'dark' : 'light');
        });
    }

    // Ensure checkbox reflects initial theme
    const initCheckbox = () => {
        const cb = document.getElementById('darkModeCheckbox');
        if (!cb) return;
        if (document.documentElement.classList.contains('dark')) cb.checked = true;
        else cb.checked = false;
    };

    initCheckbox();
});
