'use strict';

module.exports = () => ({
    open: false,

    sidebar: {
        ['x-init']() {
            this.$el.setAttribute(`x-on:open-sidebar-${this.$el.getAttribute('id')}.window`, 'open = true');
        },
        ['x-show']() {
            return this.open;
        },
        ['x-on:sidebar-open.window']() {
            this.open = true;
        },
    },

    sidebarOverlay: {
        ['x-transition:enter']: 'transition-opacity ease-linear duration-300',
        ['x-transition:enter-start']: 'opacity-0',
        ['x-transition:enter-end']: 'opacity-100',
        ['x-transition:leave']: 'transition-opacity ease-linear duration-300',
        ['x-transition:leave-start']: 'opacity-100',
        ['x-transition:leave-end']: 'opacity-0',
        ['x-show']() {
            return this.open;
        },
        [':aria-hidden']() {
            return !this.open;
        },
    },

    sidebarMenu: {
        ['x-transition:enter']: 'transition ease-in-out duration-300 transform',
        ['x-transition:enter-start']: '-translate-x-full',
        ['x-transition:enter-end']: 'translate-x-0',
        ['x-transition:leave']: 'transition ease-in-out duration-300 transform',
        ['x-transition:leave-start']: 'translate-x-0',
        ['x-transition:leave-end']: '-translate-x-full',
        ['x-show']() {
            return this.open;
        },
        ['x-on:click.outside']() {
            this.open = false;
        },
        ['x-ref']: 'menu',
    },

    sidebarCloseButton: {
        ['x-transition:enter']: 'ease-in-out duration-300',
        ['x-transition:enter-start']: 'opacity-0',
        ['x-transition:enter-end']: 'opacity-100',
        ['x-transition:leave']: 'ease-in-out duration-300',
        ['x-transition:leave-start']: 'opacity-100',
        ['x-transition:leave-end']: 'opacity-0',
        ['x-show']() {
            return this.open;
        },
        ['x-on:click']() {
            this.open = false;
        },
    },
});
