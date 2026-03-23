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
                    50: '#ffe5ea',
                    100: '#ffc6cf',
                    200: '#ffa3b3',
                    300: '#ff7f98',
                    400: '#ff5c7e',
                    500: '#ff3862',
                    600: '#FF0037',
                    700: '#D90033',
                    800: '#B2002F',
                    900: '#8C002B',
                },
            },
        },
    },

    plugins: [forms],
};
