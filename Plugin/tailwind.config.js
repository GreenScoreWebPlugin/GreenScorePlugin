/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/popup.html"],
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