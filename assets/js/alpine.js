const Alpine = require('alpinejs').default;
const Collapse = require('@alpinejs/collapse').default;
const Focus = require('@alpinejs/focus').default;
const Tooltip = require('@ryangjchandler/alpine-tooltip').default;
const Intersect = require('@alpinejs/intersect').default;
const Autosize = require('./plugin/autosize').default;
const Flatpickr = require('./plugin/flatpickr').default;
const Select = require('./plugin/select').default;
const Toggle = require('./plugin/toggle').default;
const Tags = require('./plugin/tags').default;
const Codemirror = require('./plugin/codemirror').default;
const ColorPicker = require('./plugin/color-picker').default;
const $forceNextTick = require('./magic/force-next-tick').$forceNextTick;

Alpine.plugin(Collapse);
Alpine.plugin(Focus);
Alpine.plugin(Tooltip);
Alpine.plugin(Intersect);
Alpine.plugin(Autosize);
Alpine.plugin(Flatpickr);
Alpine.plugin(Select);
Alpine.plugin(Toggle);
Alpine.plugin(Tags);
Alpine.plugin(Codemirror);
Alpine.plugin(ColorPicker);

Alpine.data('collapse', require('./components/collapse'));
Alpine.data('dashboard', require('./components/dashboard'));
Alpine.data('dropdown', require('./components/dropdown'));
Alpine.data('modal', require('./components/modal'));
Alpine.data('sidebar', require('./components/sidebar'));

Alpine.magic('forceNextTick', () => $forceNextTick);

Alpine.start();
