/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx}",
    "./components/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Poppins", "system-ui"],
      },
      boxShadow: {
        card: "0px 0px 20px 0px rgba(0, 0, 0, 0.15)",
      },
      transitionDuration: {
        DEFAULT: "300ms",
      },
      minHeight: {
        24: "96px"
      },
      colors: {
        cyan: {
          500: "#46b6bf",
          700: "#0f929a",
          200: "#bceff3"
        },
        red: {
          500: "#ee4322"
        },
        black: "#1d1e1c",
        white: "#ffffff",
        transparent: "transparent",
      },
    },
    container: {
      padding: "2rem",
      center: true,
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
  ],
};
