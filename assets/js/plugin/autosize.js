'use strict';

function Autosize(Alpine) {
    Alpine.directive('autosize', (el, {}, { cleanup }) => {

        if (!el.hasAttribute('wire:ignore') && el.hasAttribute('wire:model')) {
            el.setAttribute('wire:ignore', '');
        }

        const previousResizeValue = el.style.resize;
        const previousMinHeight = el.style.minHeight;

        const attach = () => {
            el.style.resize = 'none';
            el.style.minHeight = el.getBoundingClientRect().height + 'px';
            handler({ target: el });
        };

        const handler = (event) => {
            const element = event.target;
            if (!element.scrollHeight) {
                return;
            }

            const styles = window.getComputedStyle(el);
            const topBorderWidth = styles.getPropertyValue('border-top-width');
            const bottomBorderWidth = styles.getPropertyValue('border-bottom-width');

            element.style.height = '4px';
            element.style.height = `calc(${element.scrollHeight}px + ${topBorderWidth} + ${bottomBorderWidth})`;
        };

        attach();

        el.addEventListener('input', handler);
        el.refreshAutosize = attach;

        cleanup(() => {
            el.style.resize = previousResizeValue;
            el.style.minHeight = previousMinHeight;
            el.removeEventListener('input', handler);
        });
    });
}

export default Autosize;
