'use strict';

const Bridge = require('../modal/modal-bridge');
const BodyScrollLock = require('../body-scroll-lock');

module.exports = () => ({
    opened: false,
    name: undefined,

    open() {
        if (this.opened) {
            return;
        }

        this.$nextTick((() => {
            BodyScrollLock.lock(true);
        }));

        this.opened = true;
        Bridge.dispatchOpened(this);
    },

    close() {
        if (!this.opened) {
            return;
        }

        BodyScrollLock.unlock();

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
        ['x-on:keydown.escape.window']() {
            this.close();
        },
        ['x-show']() {
            return this.opened;
        },
        ['x-on:open-modal.window']() {
            if (this.name === this.$event.detail) {
                this.open();
            }
        },
        ['x-on:close-modal.window']() {
            if (this.name === this.$event.detail) {
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
        ['x-on:click']() {
            this.close();
        },
    },

    scrollElement: {
        ['x-ref']: 'scrollElement',
        ['x-on:click']() {
            if (this.$event.target === this.$refs.scrollElement) {
                this.close();
            }
        },
    },

    modalDialog: {
        ['x-transition:enter']: 'ease-out duration-300',
        ['x-transition:enter-start']: 'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95',
        ['x-transition:enter-end']: 'opacity-100 translate-y-0 sm:scale-100',
        ['x-transition:leave']: 'ease-in duration-200',
        ['x-transition:leave-start']: 'opacity-100 translate-y-0 sm:scale-100',
        ['x-transition:leave-end']: 'opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95',
        ['x-show']() {
            return this.opened;
        },
        ['x-ref']: 'modalDialog',
    },

    modalCloseButton: {
        ['x-on:click']() {
            this.close();
        },
    },
});
