'use strict';

module.exports = () => ({
    expanded: false,
    collapse: {
        ['x-init']() {
            if (!this.$el.querySelector('[x-bind="collapsePanel"]').hasAttribute('x-cloak')) {
                this.expanded = true;
            }
        },
        ['x-id']: '["collapse"]',
    },

    collapseButton: {
        ['x-ref']: 'button',
        ['x-on:click']() {
            this.expanded = !this.expanded;
        },
        [':aria-expanded']() {
            return this.expanded;
        },
        [':aria-controls']() {
            return this.$id('collapse');
        },
    },

    collapsePanel: {
        ['x-collapse']: true,
        ['x-show']() {
            return this.expanded
        },
        ['x-ref']: 'panel',
        [':id']() {
            return this.$id('collapse');
        },
    },
});
