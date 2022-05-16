'use strict';

module.exports = {
    listeners: {
        'init': [],
        'opened': [],
        'closed': [],
    },
    dispatchInitialized: function (modal) {
        for (let i in this.listeners.init) {
            this.listeners.init[i](modal);
        }
    },
    dispatchOpened: function (modal) {
        for (let i in this.listeners.opened) {
            this.listeners.opened[i](modal);
        }
    },
    dispatchClosed: function (modal) {
        for (let i in this.listeners.closed) {
            this.listeners.closed[i](modal);
        }
    },
    addListener: function (event, listener) {
        this.listeners[event].push(listener);
    },
};
