const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './src/Web/Ui/templates/mail/**/*.latte',
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
