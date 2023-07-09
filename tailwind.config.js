const preset = require('./vendor/filament/filament/tailwind.config.preset');

/** @type {import('tailwindcss').Config} */
module.exports = {
    presets: [preset],
    content: [
        './resources/views/**/*.blade.php',
        './src/**/*.php'
    ],
    darkMode: 'class',
    theme: {
        extend: {
            boxShadow: {
                'top': '0 -26px 25px 15px rgba(0, 0, 0, 0.7)',
            }
        },
    },
}
