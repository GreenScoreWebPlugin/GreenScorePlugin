/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./src/popup.html','./src/popup.js'],
  purge: false,
  safelist: [
    'text-[#4CAF50]', 'bg-[#4CAF50]', 'border-[#4CAF50]',
    'text-[#FFC107]', 'bg-[#FFC107]', 'border-[#FFC107]',
    'text-[#FF9800]', 'bg-[#FF9800]', 'border-[#FF9800]',
    'text-[#BD2525]', 'bg-[#FFB7B7]', 'border-[#BD2525]'
  ],
  theme: {
    extend: {
      colors: {
        "grey-100": "#ECECEC",
        "grey-400": "#979797",
        "grey-950": "#030712",

        "gs-green-950": "#233430"


      },
      fontFamily: {
          outfit: ["Outfit", "sans-serif"],
      },
    },
  },
  plugins: [],
}