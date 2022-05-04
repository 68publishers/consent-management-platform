module.exports = {
  content: [
    './src/**/*.latte',
    './assets/js/**/*.js',
  ],
  safelist: [
    {
      pattern: /text-(center|left|right)/,
    },
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
