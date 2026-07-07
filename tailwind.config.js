import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Alpine.js :class bindings — Tailwind không scan nội dung của :class="..."
        'lg:pl-64', 'lg:pl-16', 'lg:w-64', 'lg:w-16',
        'lg:translate-x-0', '-translate-x-full', 'translate-x-0',
        'rotate-180',
        // Quick create menu colours (badge dots)
        'bg-blue-500', 'bg-purple-500', 'bg-rose-500', 'bg-emerald-500', 'bg-amber-500',
        // Background colours used in avatar fallback inside Alpine loops
        'bg-indigo-100', 'text-indigo-600',
        // User role badges (Alpine :class object in quick-view modal)
        'bg-red-100', 'text-red-700',
        'bg-purple-100', 'text-purple-700',
        'bg-blue-100', 'text-blue-700',
        'bg-indigo-100', 'text-indigo-700',
        // Trix editor accents used dynamically
        'ring-indigo-500', 'border-indigo-500',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
