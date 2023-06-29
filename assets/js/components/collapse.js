'use strict';

module.exports = () => ({
    expanded: false,
    storageName: null,
    storageKey: null,

    init() {
        let expanded = undefined;
        const storageName = this.$el.getAttribute('data-storage-name');
        const storageKey = this.$el.getAttribute('data-storage-key');

        if (storageName) {
            this.storageName = storageName;
        }

        if (storageKey) {
            this.storageKey = storageKey;
        }

        if ('string' === typeof this.storageName && 'string' === typeof this.storageKey) {
            let store;

            try {
                store = JSON.parse(window.sessionStorage.getItem(this.storageName) || '{}');
            } catch (err) {
                // ignore
            }

            if (store && this.storageKey in store) {
                expanded = store[this.storageKey];
            }
        }

        if (undefined === expanded && !this.$el.querySelector('[x-bind="collapsePanel"]').hasAttribute('x-cloak')) {
            expanded = true;
        }

        this.expanded = undefined !== expanded ? expanded : false;
    },
    collapse: {
        ['x-id']: '["collapse"]',
    },

    collapseButton: {
        ['x-ref']: 'button',
        ['x-on:click']() {
            this.expanded = !this.expanded;

            if ('string' === typeof this.storageName && 'string' === typeof this.storageKey) {
                let store = {};

                try {
                    store = JSON.parse(window.sessionStorage.getItem(this.storageName) || '{}');
                } catch (err) {
                    // ignore
                }

                store[this.storageKey] = this.expanded;

                window.sessionStorage.setItem(this.storageName, JSON.stringify(store));
            }
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
