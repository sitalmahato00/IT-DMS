import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // Enable manual (class-based) dark mode so we can toggle it from JS
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/**/*.php',
        './app/View/Components/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                red: {
                    50:  '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#f43f5e',
                    600: '#dc2626',   /* ← system primary red  */
                    700: '#b91c1c',   /* ← system dark red     */
                    800: '#991b1b',   /* ← system deeper red   */
                    900: '#7f1d1d',   /* ← system maroon/hero  */
                    950: '#450a0a',
                },
            },
        },
    },

    plugins: [forms],
};
