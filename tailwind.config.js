import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/Filament/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                navy: {
                    50:  '#E8ECF2',
                    100: '#C5CEDC',
                    200: '#9FACC2',
                    300: '#778AA7',
                    400: '#566E92',
                    500: '#35527E',
                    600: '#1F3A63',
                    700: '#13315C',
                    800: '#0B2545',
                    900: '#06182F',
                    950: '#030D1C',
                },
                petrol: {
                    DEFAULT: '#13315C',
                    light:   '#1F4378',
                    dark:    '#0E2447',
                },
                brass: {
                    50:  '#FAF1E3',
                    100: '#F1DDB6',
                    200: '#E5C284',
                    300: '#D6A453',
                    400: '#C58E3F',
                    500: '#B87333',
                    600: '#9B5F28',
                    700: '#7C4B1F',
                    800: '#5C3717',
                    900: '#3D240F',
                },
                graphite: '#1F2937',
                cream:    '#F4F4F2',
            },
            fontFamily: {
                sans:    ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans],
                mono:    ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
        },
    },
    plugins: [forms, typography],
};
