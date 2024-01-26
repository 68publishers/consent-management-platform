const Clipboard = require('clipboard');
const tippy = require('tippy.js').default;

(function () {
    (ready => {
        if (document.readyState !== 'loading') {
            ready();
            return;
        }

        document.addEventListener('DOMContentLoaded', ready);
    })(() => {
        const timeouts = new WeakMap();

        const showTooltip = (trigger, text) => {
            if (!trigger._tippy) {
                tippy(trigger, {
                    content: '',
                    placement: 'bottom',
                    trigger: 'manual',
                })
            }

            tooltip = trigger._tippy;
            tooltip.setContent(text);
            tooltip.show();

            if (timeouts.has(trigger)) {
                clearTimeout(timeouts.get(trigger));
            }

            const timeout = setTimeout(() => {
                tooltip.hide();
                clearTimeout(timeouts.get(trigger));
            }, 1500);

            timeouts.set(trigger, timeout);
        };

        const cp = new Clipboard('[data-clipboard]');

        cp.on('success', function (e) {
            e.clearSelection();

            const tooltipText = e.trigger.dataset.clipboardSuccessTooltip || undefined;

            if (tooltipText) {
                showTooltip(e.trigger, tooltipText);
            }
        });

        cp.on('error', function (e) {
            e.clearSelection();

            const tooltipText = e.trigger.dataset.clipboardErrorTooltip || undefined;

            if (tooltipText) {
                showTooltip(e.trigger, tooltipText);
            }
        });
    });
})();
