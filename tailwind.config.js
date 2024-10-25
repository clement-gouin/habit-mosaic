const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.vue'
    ],
    safelist: [
        { pattern: /alert-.*/ },
        { pattern: /checkbox-.*/ },
        { pattern: /toggle-.*/ },
        { pattern: /input-.*/ },
        { pattern: /grid-cols-*/ },
        { pattern: /text-*-*/ },
        { pattern: /bg-*-*/ },
        { pattern: /border-*-*/ }
    ],
    theme: {
        extend: {
            colors: {
                primary: '#C2185B',
                secondary: '#EC407A'
            }
        }
    },
    daisyui: { themes: ['light'] },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/container-queries'),
        require('daisyui')
    ]
};
