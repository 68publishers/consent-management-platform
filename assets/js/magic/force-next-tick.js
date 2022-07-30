'use strict';

// @see https://github.com/alpinejs/alpine/discussions/2855

const doubleRequestAnimationFrame = (callback) => {
    requestAnimationFrame(() => {
        requestAnimationFrame(callback)
    })
};

export function $forceNextTick(callback = () => {}) {
    if (callback && typeof callback === 'function') {
        doubleRequestAnimationFrame(callback)
    } else {
        return new Promise(resolve => {
            doubleRequestAnimationFrame(resolve)
        })
    }
}
