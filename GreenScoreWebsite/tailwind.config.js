/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      fontFamily: {
        'outfit': ['Outfit', 'sans-serif'],
      },
      colors: {
        'gs-green-950': '#233430',
        'gs-green-800': '#2E4D49',
        'gs-green-700': '#3B6A69',
        'gs-green-600': '#4F8B8A',
        'gs-green-500': '#5FBCBB',
        'gs-green-400': '#7DD9D8',
        'gs-green-300': '#A8E8E7',
        'gs-green-200': '#C7F5F4',
        'gs-green-100': '#E3FAFA',

        'grey-950': '#030712',
        'grey-700': '#424E62',
        'grey-500': '#667991',
        'grey-400': '#8595AB',
        'grey-200': '#D5DBE2',
        'grey-100': '#ECECEC'
      }
    },
  },
  plugins: [],
}