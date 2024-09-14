/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './templates/**/*.html.twig',
    './assets/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        'merriweather-sans': ['Merriweather Sans', 'sans-serif'],
        'montserrat': ['Montserrat', 'sans-serif'],
        'raleway': ['Raleway', 'sans-serif'],
      }
    },
  },
  plugins: [],
}


