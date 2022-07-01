const Alpine = require('alpinejs').default;
const Collapse = require('@alpinejs/collapse').default;
const Autosize = require('@marcreichel/alpine-autosize').default;
const Tooltip = require('@ryangjchandler/alpine-tooltip').default;
const Flatpickr = require('./plugin/flatpickr').default;
const Select = require('./plugin/select').default;
const Toggle = require('./plugin/toggle').default;
const Tags = require('./plugin/tags').default;
const Codemirror = require('./plugin/codemirror').default;

Alpine.plugin(Collapse);
Alpine.plugin(Autosize);
Alpine.plugin(Tooltip);
Alpine.plugin(Flatpickr);
Alpine.plugin(Select);
Alpine.plugin(Toggle);
Alpine.plugin(Tags);
Alpine.plugin(Codemirror);

Alpine.data('collapse', require('./components/collapse'));
Alpine.data('dropdown', require('./components/dropdown'));
Alpine.data('modal', require('./components/modal'));
Alpine.data('sidebar', require('./components/sidebar'));

Alpine.start();
