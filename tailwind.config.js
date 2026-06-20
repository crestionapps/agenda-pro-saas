/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./resources/**/*.blade.php', './public/assets/**/*.js'],
  theme: {
    extend: {
      colors: {
        coral: {
          50: '#fdf2f4', 100: '#fce7eb', 200: '#f9d0d9', 300: '#f4a9b9',
          400: '#ec7894', 500: '#e04f73', 600: '#c9365a', 700: '#ab2846',
          800: '#8f243d', 900: '#782238',
        },
      },
    },
  },
  plugins: [],
};
