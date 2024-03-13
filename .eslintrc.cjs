module.exports = {
    root: true,
    env: {
        browser: true,
        es2021: true
    },
    globals: {
        __ICONS__: true,
        __ICON_SEARCHES__: true,
        __ICON_STYLES__: true,
    },
    ignorePatterns: ['.eslintrc.js'],
    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-essential',
        'plugin:@typescript-eslint/recommended',
        'standard-with-typescript'
    ],
    overrides: [],
    parser: 'vue-eslint-parser',
    parserOptions: {
        parser: '@typescript-eslint/parser',
        ecmaVersion: 'latest',
        sourceType: 'module',
        project: ['./tsconfig.json'],
        tsconfigRootDir: __dirname,
        extraFileExtensions: ['.vue']
    },
    plugins: [
        'vue',
        '@typescript-eslint'
    ],
    rules: {
        'array-bracket-newline': [
            'warn',
            { multiline: true }
        ],
        'array-element-newline': [
            'warn',
            'consistent'
        ],
        'array-bracket-spacing': [
            'warn',
            'never'
        ],
        'arrow-parens': [
            'warn',
            'as-needed'
        ],
        'arrow-spacing': 'warn',
        'block-spacing': 'warn',
        'brace-style': [
            'warn',
            '1tbs',
            { allowSingleLine: true }
        ],
        'comma-spacing': [
            'warn',
            {
                before: false,
                after: true
            }
        ],
        'comma-style': [
            'warn',
            'last'
        ],
        'computed-property-spacing': [
            'warn',
            'never'
        ],
        'dot-location': [
            'warn',
            'property'
        ],
        'eol-last': [
            'warn',
            'always'
        ],
        'func-call-spacing': [
            'warn',
            'never'
        ],
        'function-call-argument-newline': [
            'warn',
            'consistent'
        ],
        'function-paren-newline': [
            'warn',
            'multiline'
        ],
        'generator-star-spacing': [
            'warn',
            {
                before: true,
                after: false
            }
        ],
        'implicit-arrow-linebreak': [
            'warn',
            'beside'
        ],
        'indent': [
            'warn',
            4
        ],
        'jsx-quotes': [
            'warn',
            'prefer-double'
        ],
        'key-spacing': [
            'warn',
            {
                beforeColon: false,
                afterColon: true
            }
        ],
        'keyword-spacing': [
            'warn',
            {
                before: true,
                after: true
            }
        ],
        'linebreak-style': [
            'warn',
            'unix'
        ],
        'lines-between-class-members': [
            'warn',
            'never'
        ],
        'multiline-ternary': [
            'warn',
            'always-multiline'
        ],
        'new-parens': 'warn',
        'newline-per-chained-call': [
            'warn',
            { ignoreChainWithDepth: 2 }
        ],
        'no-extra-parens': 'warn',
        'no-multi-spaces': 'warn',
        'no-multiple-empty-lines': 'warn',
        'no-trailing-spaces': 'warn',
        'no-whitespace-before-property': 'warn',
        'nonblock-statement-body-position': [
            'warn',
            'beside'
        ],
        'object-curly-newline': [
            'warn',
            { multiline: true }
        ],
        'object-curly-spacing': [
            'warn',
            'always'
        ],
        'object-property-newline': 'warn',
        'padded-blocks': [
            'warn',
            'never'
        ],
        quotes: [
            'warn',
            'single'
        ],
        semi: [
            'warn',
            'always'
        ],
        'semi-spacing': 'warn',
        'semi-style': [
            'warn',
            'last'
        ],
        'space-before-blocks': 'warn',
        'space-in-parens': [
            'warn',
            'never'
        ],
        'space-infix-ops': 'warn',
        'space-unary-ops': 'warn',
        'switch-colon-spacing': 'warn',
        'template-curly-spacing': 'warn',
        'template-tag-spacing': 'warn',
        'wrap-iife': [
            'warn',
            'outside'
        ],
        'yield-star-spacing': [
            'warn',
            'before'
        ],
        '@typescript-eslint/semi': [
            'warn',
            'always'
        ],
        '@typescript-eslint/indent': [
            'warn',
            4
        ],
        'vue/multi-word-component-names': 'off',
        'vue/no-dupe-keys': 'off',
        'no-console': 'error'
    }
};
