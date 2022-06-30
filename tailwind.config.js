module.exports = {
  content: [
    './src/**/*.latte',
    './assets/js/**/*.js',
    './translations/**/*.neon',
  ],
  safelist: [
    {
      pattern: /text-(center|left|right)/,
    },
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...require('tailwindcss/defaultTheme').fontFamily.sans],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
