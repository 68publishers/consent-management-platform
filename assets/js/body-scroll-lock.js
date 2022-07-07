'use strict';

module.exports = (function () {
    let previousBodyOverflowSetting;
    let previousBodyPaddingRight;

    return {
        lock: reserveScrollBarGap => {
            if (previousBodyPaddingRight === undefined) {
                const scrollBarGap = window.innerWidth - document.documentElement.clientWidth;

                if (reserveScrollBarGap && scrollBarGap > 0) {
                    const computedBodyPaddingRight = parseInt(window.getComputedStyle(document.body).getPropertyValue('padding-right'), 10);
                    previousBodyPaddingRight = document.body.style.paddingRight;
                    document.body.style.paddingRight = `${computedBodyPaddingRight + scrollBarGap}px`;
                }
            }

            if (previousBodyOverflowSetting === undefined) {
                previousBodyOverflowSetting = document.body.style.overflow;
                document.body.style.overflow = 'hidden';
            }
        },
        unlock: () => {
            if (previousBodyPaddingRight !== undefined) {
                document.body.style.paddingRight = previousBodyPaddingRight;
                previousBodyPaddingRight = undefined;
            }

            if (previousBodyOverflowSetting !== undefined) {
                document.body.style.overflow = previousBodyOverflowSetting;
                previousBodyOverflowSetting = undefined;
            }
        },
    };
})();
