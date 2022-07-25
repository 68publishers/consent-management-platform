module.exports = {
    plugins: {
        'postcss-import': {
            path: './assets/css/mail',
        },
        'postcss-custom-properties': {
            preserve: false,
        },
        'autoprefixer': {},
    },
};
