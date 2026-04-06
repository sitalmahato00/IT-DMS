import './bootstrap';

// Initialize session keep-alive to prevent 419 PAGE EXPIRED errors
import './session-keep-alive';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';
import $ from 'jquery';

if (document.body?.hasAttribute('data-mobile-shell')) {
    import('./mobile-app').catch((error) => {
        console.warn('Mobile app runtime failed to load:', error);
    });
}

// Make jQuery available globally (nepali-date-picker expects global jQuery)
globalThis.$ = globalThis.jQuery = $;

window.Alpine = Alpine;
window.Chart = Chart;

// Register Alpine plugins
Alpine.plugin(collapse);

Alpine.start();

// JS: Nepali (BS) datepicker integration
import 'nepali-date-picker/dist/nepaliDatePicker.min.css';

let bsDatePickerPromise = null;
function ensureBsDatePickerLoaded() {
    if (bsDatePickerPromise) return bsDatePickerPromise;
    bsDatePickerPromise = import('nepali-date-picker')
        .then(() => true)
        .catch((e) => {
            console.warn('Nepali datepicker load failed:', e);
            return false;
        });
    return bsDatePickerPromise;
}

function nepaliDigitsToEnglish(str) {
    if (!str) return str;
    const map = { '०':'0','१':'1','२':'2','३':'3','४':'4','५':'5','६':'6','७':'7','८':'8','९':'9' };
    return str.replace(/[०-९]/g, d => map[d] || d);
}

function englishDigitsToNepali(str) {
    if (!str) return str;
    const map = { '0':'०','1':'१','2':'२','3':'३','4':'४','5':'५','6':'६','7':'७','8':'८','9':'९' };
    return String(str).replace(/[0-9]/g, d => map[d] || d);
}

function initBsDatePicker(root = document) {
    const $inputs = $(root).find('input.bs-date').addClass('date-picker');
    try {
        if (!$.fn || typeof $.fn.nepaliDatePicker !== 'function') {
            // Plugin not loaded yet; caller should run ensureBsDatePickerLoaded() first.
            return;
        }
        $inputs.each(function() {
            if (!$(this).data('bs-initialized')) {
                // Remove readonly if set, allow manual editing
                $(this).removeAttr('readonly');
                
                $(this).nepaliDatePicker({
                    dateFormat: '%y-%m-%d',
                    closeOnDateSelect: true
                });
                
                // Ensure input is not readonly after initialization
                $(this).removeAttr('readonly');
                $(this).css('cursor', 'text');
                
                // Normalize any pre-filled value to Devanagari digits for display.
                $(this).val(englishDigitsToNepali($(this).val() || ''));
                $(this).on('change.bsDatepicker', function() {
                    // Keep BS input display in Devanagari digits for Nepali UX.
                    const before = $(this).val() || '';
                    const after = englishDigitsToNepali(before);
                    if (before !== after) {
                        $(this).val(after);
                    }
                    // Ensure downstream listeners (AD<->BS sync) run.
                    const inputEl = this;
                    inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                    inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                });
                // Some versions of the plugin set value without firing native events.
                // Ensure downstream listeners (AD<->BS sync) run when the value changes.
                $(this).on('focus.bsDatepickerSync', function() {
                    $(this).data('bs-prev-value', $(this).val() || '');
                    if (!$(this).data('bs-sync-timer')) {
                        const inputEl = this;
                        const timer = setInterval(() => {
                            const prev = $(inputEl).data('bs-prev-value') || '';
                            const now = $(inputEl).val() || '';
                            if (prev !== now) {
                                $(inputEl).data('bs-prev-value', now);
                                inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                                inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }, 200);
                        $(this).data('bs-sync-timer', timer);
                    }
                });
                $(this).on('blur.bsDatepickerSync', function() {
                    const timer = $(this).data('bs-sync-timer');
                    if (timer) {
                        clearInterval(timer);
                        $(this).removeData('bs-sync-timer');
                    }
                    const prev = $(this).data('bs-prev-value') || '';
                    const now = $(this).val() || '';
                    if (prev !== now) {
                        this.dispatchEvent(new Event('input', { bubbles: true }));
                        this.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                $(this).data('bs-initialized', true);
            }
        });
    } catch (e) {
        console.warn('Nepali datepicker init failed:', e);
    }
}

// Expose manual initializer/opener (used by Blade onclick handlers)
window.initBsDatePicker = initBsDatePicker;
window.openBsDatePicker = function (inputOrId) {
    const el = typeof inputOrId === 'string' ? document.getElementById(inputOrId) : inputOrId;
    if (!el) return;

    ensureBsDatePickerLoaded().then((ok) => {
        if (!ok) {
            try { window.showToast?.('BS date picker failed to load', 'error'); } catch (e) {}
            return;
        }

        // Ensure the input has bs-date class so the plugin initializes it
        if (!el.classList.contains('bs-date')) {
            el.classList.add('bs-date');
        }

        // Initialize the date picker for this specific input
        initBsDatePicker(el.parentElement || document);
        
        // Delay open until after the current click event finishes,
        // otherwise "outside click" handlers can instantly close the picker.
        setTimeout(() => {
            try {
                const $el = $(el);
                $el.trigger('focus');
                $el.trigger('click');
                $el.trigger('mousedown');
            } catch (e) {}

            try {
                el.focus({ preventScroll: true });
            } catch (e) {
                el.focus();
            }

            ['mousedown', 'mouseup', 'click'].forEach(type => {
                try {
                    el.dispatchEvent(new MouseEvent(type, { bubbles: true, cancelable: true, view: window }));
                } catch (e) {}
            });
        }, 0);
    });
};

let themeMediaListenerRegistered = false;

const AUTH_ROUTES = [
    'login',
    'register',
    'password.request',
    'password.reset',
    'password.confirm',
    'verification.send',
    'verification.verify',
    'two-factor.login',
    'two-factor.confirm',
];

function isAuthPage() {
    const routeName = document.body.getAttribute('data-mobile-route');
    return AUTH_ROUTES.includes(routeName);
}

function getThemePreference() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || savedTheme === 'light') {
        return savedTheme;
    }

    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }

    return 'light';
}

function getDarkModeButtons(root = document) {
    return Array.from(root.querySelectorAll('#darkModeToggle'));
}

function updateThemeIcons(theme) {
    const isDark = theme === 'dark';
    const darkModeIcon = document.getElementById('darkModeIcon');
    const moonIconSvg = document.getElementById('moonIcon');
    const sunIconSvg = document.getElementById('sunIcon');

    if (darkModeIcon) {
        darkModeIcon.classList.toggle('bi-moon-fill', !isDark);
        darkModeIcon.classList.toggle('bi-sun-fill', isDark);
    }

    if (moonIconSvg && sunIconSvg) {
        moonIconSvg.classList.toggle('hidden', isDark);
        sunIconSvg.classList.toggle('hidden', !isDark);
    }

    getDarkModeButtons().forEach((button) => {
        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        button.title = isDark ? 'Switch to light mode' : 'Switch to dark mode';
    });
}

function applyTheme(theme) {
    const html = document.documentElement;

    // Force light mode on auth pages
    if (isAuthPage()) {
        theme = 'light';
        localStorage.setItem('theme', 'light');
    }

    if (theme === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }

    localStorage.setItem('theme', theme);
    updateThemeIcons(theme);
}

function toggleTheme() {
    // Don't allow toggling theme on auth pages
    if (isAuthPage()) {
        return;
    }

    const html = document.documentElement;
    const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    applyTheme(newTheme);
}

function initThemeControls(root = document) {
    applyTheme(getThemePreference());

    getDarkModeButtons(root).forEach((button) => {
        if (button.dataset.themeToggleBound === 'true') {
            return;
        }

        button.dataset.themeToggleBound = 'true';
        button.addEventListener('click', toggleTheme);
    });

    if (!themeMediaListenerRegistered && window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });

        themeMediaListenerRegistered = true;
    }
}

window.reinitializeThemeToggle = initThemeControls;
window.ensureBsDatePickerLoaded = ensureBsDatePickerLoaded;
window.reinitializeBaseUi = async function (root = document) {
    initThemeControls(root);

    const ok = await ensureBsDatePickerLoaded();
    if (ok) {
        initBsDatePicker(root);
    }
};

document.addEventListener('DOMContentLoaded', async () => {
    const ok = await ensureBsDatePickerLoaded();
    if (ok) initBsDatePicker();
    initThemeControls(document);

    // Watch for dynamically added inputs (modals, AJAX content)
    const obs = new MutationObserver((mutations) => {
        mutations.forEach(m => {
            m.addedNodes && m.addedNodes.forEach(node => {
                try {
                    if (node.querySelectorAll && node.querySelectorAll('input.bs-date').length) {
                        ensureBsDatePickerLoaded().then((loaded) => {
                            if (loaded) initBsDatePicker(node);
                        });
                    }
                } catch (e) {}
            });
        });
    });

    obs.observe(document.body, { childList: true, subtree: true });

});

// Keep BS inputs displayed using Devanagari digits, but submit English digits to the server.
document.addEventListener('blur', (e) => {
    const el = e.target;
    if (!(el instanceof HTMLInputElement)) return;
    // Handle both bs-date class and DOB input specifically
    if (!el.classList.contains('bs-date') && el.name !== 'dob_bs') return;
    el.value = englishDigitsToNepali(el.value || '');
}, true);

document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    // Process inputs with bs-date class
    const inputs = Array.from(form.querySelectorAll('input.bs-date'));
    
    // Also process the DOB input even if it doesn't have bs-date class
    const dobInput = form.querySelector('input[name="dob_bs"]');
    if (dobInput && !inputs.includes(dobInput)) {
        inputs.push(dobInput);
    }
    
    if (inputs.length === 0) return;

    const originals = inputs.map((input) => ({ input, value: input.value }));
    originals.forEach(({ input, value }) => {
        input.value = nepaliDigitsToEnglish(value || '');
    });

    // Restore UI values if submission is prevented / handled via AJAX.
    setTimeout(() => {
        originals.forEach(({ input, value }) => {
            if (document.contains(input)) {
                input.value = value;
            }
        });
    }, 0);
}, true);
