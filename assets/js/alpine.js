const Alpine = require('alpinejs').default;
const Collapse = require('@alpinejs/collapse').default;
const Autosize = require('@marcreichel/alpine-autosize').default;

Alpine.plugin(Collapse);
Alpine.plugin(Autosize);

Alpine.data('dropdown', require('./components/dropdown'));
Alpine.data('sidebar', require('./components/sidebar'));
Alpine.data('collapse', require('./components/collapse'));
Alpine.data('modal', require('./components/modal'));

Alpine.start();
