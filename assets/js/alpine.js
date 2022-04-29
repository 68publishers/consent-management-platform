const Alpine = require('alpinejs').default;
const collapse = require('@alpinejs/collapse').default;

Alpine.plugin(collapse);

Alpine.data('dropdown', require('./components/dropdown'));
Alpine.data('sidebar', require('./components/sidebar'));
Alpine.data('collapse', require('./components/collapse'));

Alpine.start();
