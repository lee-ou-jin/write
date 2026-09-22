import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const tailwindColors = require( './tailwind.color.ts' );

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',

        './amuz-themes/**/**/resources/views/**/*.blade.php',
        './amuz-themes/**/**/resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            animation: {
                'spin-slow': 'spin 5s linear infinite',
            },
            fontFamily: {
                sans: ['Noto Sans KR', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'amuz-primary': '#2E6EF3',
                ...tailwindColors,
            },
        },
    },

    plugins: [forms, typography],
};
