const colors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {
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
        { pattern: /bg-primary/ },
        { pattern: /border-*-*/ }
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#FCE4EC',
                    100: '#F8BBD0',
                    200: '#F48FB1',
                    300: '#F06292',
                    400: '#EC407A',
                    500: '#E91E63',
                    600: '#D81B60',
                    700: '#C2185B',
                    800: '#AD1457',
                    900: '#880E4F'
                }
            }
        }
    },
    daisyui: {
        themes: [
            {
                light: {
                    ...require('daisyui/src/theming/themes').light,
                    primary: '#D81B60',
                    secondary: '#EC407A'
                }
            }
        ]
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/container-queries'),
        require('daisyui')
    ]
};
