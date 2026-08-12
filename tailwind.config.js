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
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // AUN brand palette (matched from aun.edu.ng + the E-Laundry logo)
                aun: {
                    navy: '#222454',
                    'navy-dark': '#1A1B4B',
                    'navy-light': '#2f3170',
                    orange: '#F75B30',
                    'orange-dark': '#EF6D00',
                    green: '#0FA153',
                    red: '#C8262A',
                },
            },
        },
    },

    plugins: [forms],
};
