'use strict';

const Bridge = require('../modal/modal-bridge');

module.exports = () => ({
    opened: false,
    name: undefined,

    open() {
        if (this.opened) {
            return;
        }

        this.opened = true;
        Bridge.dispatchOpened(this);
    },
    close() {
        if (!this.opened) {
            return;
        }

        this.opened = false;

        setTimeout(() => {
            Bridge.dispatchClosed(this);
        }, 200);
    },
    destroy() {
        this.$el.remove();
    },

    modal: {
        ['x-init']() {
            this.name = this.$el.getAttribute('data-modal-name');
            Bridge.dispatchInitialized(this);
        },
        ['x-on:keydown.escape']() {
            this.close();
        },
        ['x-show']() {
            return this.opened;
        },
        ['x-on:open-modal.window']() {
            if (name === this.$event.detail) {
                this.open();
            }
        },
        ['x-on:close-modal.window']() {
            if (name === this.$event.detail) {
                this.close();
            }
        },
    },

    modalOverlay: {
        ['x-transition:enter']: 'ease-out duration-300',
        ['x-transition:enter-start']: 'opacity-0',
        ['x-transition:enter-end']: 'opacity-100',
        ['x-transition:leave']: 'ease-in duration-200',
        ['x-transition:leave-start']: 'opacity-100',
        ['x-transition:leave-end']: 'opacity-0',
        ['x-show']() {
            return this.opened;
        },
    },

    modalDialog: {
        ['x-transition:enter']: 'ease-out duration-300',
        ['x-transition:enter-start']: 'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95',
        ['x-transition:enter-end']: 'opacity-100 translate-y-0 sm:scale-100',
        ['x-transition:leave']: 'ease-in duration-200',
        ['x-transition:leave-start']: 'opacity-100 translate-y-0 sm:scale-100',
        ['x-transition:leave-end']: 'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95',
        ['x-on:click.outside']() {
            this.close();
        },
        ['x-show']() {
            return this.opened
        },
    },

    modalCloseButton: {
        ['x-on:click']() {
            this.close();
        },
    },
});
