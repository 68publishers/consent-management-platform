const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './src/Web/Ui/templates/mail/**/*.latte',
    './translations/mail.*.neon',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
