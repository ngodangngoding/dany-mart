module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: [
                    'Instrument Sans',
                    'ui-sans-serif',
                    'system-ui',
                    'sans-serif',
                    'Apple Color Emoji',
                    'Segoe UI Emoji',
                    'Segoe UI Symbol',
                    'Noto Color Emoji',
                ],
            },
            colors: {
                kasir: {
                    bg: '#ffffff',
                    'card-border': '#d9dde3',
                    'input-border': '#e5e7eb',
                    muted: '#7a7d85',
                    pastel: '#e8f1da',
                    'pastel-hover': '#dce9c7',
                    primary: '#609824',
                    'primary-dark': '#4f7f1d',
                    surface: '#f7f8f3',
                    danger: '#ff6b6b',
                    'danger-soft': '#fff4f4',
                },
            },
        },
    },
    plugins: [],
};
