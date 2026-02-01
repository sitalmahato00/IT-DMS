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
    const THEME_KEY = 'theme';
    const getToggleElement = () => document.getElementById('theme-toggle') || document.getElementById('darkModeCheckbox');

    function applyTheme(theme, save = true) {
        const togg = getToggleElement();

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            if (togg) togg.checked = true;
        } else {
            document.documentElement.classList.remove('dark');
            if (togg) togg.checked = false;
        }

        if (save) {
            try { localStorage.setItem(THEME_KEY, theme); } catch(e){}
        }
        console.info('[Theme] applied:', theme);

        // Update accessibility state if the toggle supports it
        if (togg) togg.setAttribute('aria-checked', theme === 'dark' ? 'true' : 'false');

        // Update visible status indicator if present (for debugging/UX)
        const statusEl = document.getElementById('theme-status');
        if (statusEl) statusEl.textContent = theme === 'dark' ? 'Dark' : 'Light';
    }

    // Initialize theme from localStorage or prefers-color-scheme. If user sets a preference we respect it; else follow system and listen for changes.
    const saved = (() => { try { return localStorage.getItem(THEME_KEY); } catch(e){return null;} })();
    if (saved) applyTheme(saved, false);
    else {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) applyTheme('dark', false);
        if (window.matchMedia) {
            const mq = window.matchMedia('(prefers-color-scheme: dark)');
            mq.addEventListener && mq.addEventListener('change', e => {
                const savedNow = (() => { try { return localStorage.getItem(THEME_KEY); } catch(e){return null;} })();
                if (!savedNow) applyTheme(e.matches ? 'dark' : 'light', false);
            });
        }
    }

    // Toggle handler (supports either id)
    const toggle = getToggleElement();
    if (toggle) {
        console.log('Theme toggle found, attaching event listener');
        toggle.addEventListener('change', function() {
            applyTheme(this.checked ? 'dark' : 'light', true);
        });
    } else {
        console.warn('Theme toggle not found in DOM');
    }

    // Fallback: force toggle on label click (some CSS may prevent default toggling in certain cases)
    const labelEl = document.querySelector('label[for="theme-toggle"]');
    if (labelEl) {
        labelEl.addEventListener('click', () => {
            const t = getToggleElement();
            if (t) {
                // allow default label behavior to run (toggled) then sync theme
                setTimeout(() => {
                    console.log('Label click detected, syncing theme to checkbox:', t.checked);
                    applyTheme(t.checked ? 'dark' : 'light', true);
                }, 0);
            } else {
                const now = document.documentElement.classList.toggle('dark');
                try { localStorage.setItem(THEME_KEY, now ? 'dark' : 'light'); } catch(e){}
                console.log('Label click toggled theme directly to', now ? 'dark' : 'light');
                const se = document.getElementById('theme-status');
                if (se) se.textContent = now ? 'Dark' : 'Light';
            }
        });
    }

    // Ensure checkbox reflects initial theme on load
    const initCheckbox = () => {
        const cb = getToggleElement();
        if (!cb) return;
        cb.checked = document.documentElement.classList.contains('dark');
        cb.setAttribute('aria-checked', cb.checked ? 'true' : 'false');
        const se = document.getElementById('theme-status');
        if (se) se.textContent = cb.checked ? 'Dark' : 'Light';
    };

    initCheckbox();
});
