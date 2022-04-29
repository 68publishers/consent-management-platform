'use strict';

module.exports = () => ({
    open: false,
    toggle() {
        if (this.open) {
            return this.close();
        }

        this.$refs.button.focus();
        this.open = true;
    },
    close(focusAfter) {
        if (!this.open) {
            return;
        }

        this.open = false;

        focusAfter && focusAfter.focus();
    },
    dropdown: {
        ['x-on:keydown.escape.prevent.stop']() {
            this.close();
        },
        ['x-on:focusin.window']() {
            !this.$refs.panel.contains(this.$event.target) && this.close();
        },
        ['x-id']: '["dropdown"]',
    },

    dropdownButton: {
        ['x-ref']: 'button',
        ['x-on:click']() {
            this.toggle();
        },
        [':aria-expanded']() {
            return this.open;
        },
        [':aria-controls']() {
            return this.$id('dropdown');
        },
    },

    dropdownPanel: {
        ['x-transition:enter']: 'transition ease-out duration-100',
        ['x-transition:enter-start']: 'transform opacity-0 scale-95',
        ['x-transition:enter-end']: 'transform opacity-100 scale-100',
        ['x-transition:leave']: 'transition ease-in duration-75',
        ['x-transition:leave-start']: 'transform opacity-100 scale-100',
        ['x-transition:leave-end']: 'transform opacity-0 scale-95',
        ['x-on:click.outside']() {
            this.close(this.$refs.button);
        },
        ['x-show']() {
            return this.open
        },
        ['x-ref']: 'panel',
        [':id']() {
            return this.$id('dropdown');
        },
    },
});
