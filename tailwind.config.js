/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./app/View/**/*.{html,php,js,ts}"],
  theme: {
    screens: {
      "3xs": "320px",
      "2xs": "425px",
      xs: "480px",
      sm: "640px",
      md: "768px",
      lg: "1024px",
      xl: "1280px",
      "2xl": "1440px",
      "3xl": "1920px",
    },
    extend: {
      colors: {
        primary: {},
        secondary: {},
      },
    },
  },
  plugins: [],
};
