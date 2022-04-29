'use strict';

const scriptJs = require('scriptjs');

scriptJs('https://www.google.com/recaptcha/api.js?render=explicit', function() {
    require('Vendor/contributte/recaptcha/assets/invisibleRecaptcha.js');
});
