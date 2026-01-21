import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                "primary": "#137fec",
                "background-light": "#f8f9fa",
                "background-dark": "#101922",
                "surface-dark": "#1a2634",
                "surface-light": "#ffffff",
                "secondary": "#FF6B35",
                "highlight": "#A3FF00",
                "primary-hover": "#0f6ac6", // Retaining helper shade
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Inter', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
