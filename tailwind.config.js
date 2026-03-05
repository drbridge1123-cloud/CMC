/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './frontend/**/*.php',
    './frontend/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        franklin: ['Libre Franklin', 'sans-serif'],
      },
      colors: {
        navy: {
          DEFAULT: '#0F1B2D',
          50:  '#f0f3ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          800: '#1e1b4b',
          900: '#0f0d2e',
          light:  '#1A2A40',
          border: '#243347',
        },
        gold: {
          DEFAULT: '#C9A84C',
          hover: '#B8973F',
        },
        'v2-bg':          '#FFFFFF',
        'v2-card':        '#FFFFFF',
        'v2-card-border': '#E5E5E0',
        'v2-card-bg':     '#F0F2F5',
        'v2-text':        '#0F1B2D',
        'v2-text-mid':    '#3D4F63',
        'v2-text-light':  '#5A6B82',
      },
    },
  },
  plugins: [],
};
