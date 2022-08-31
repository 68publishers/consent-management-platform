'use strict';

function Flatpickr(Alpine) {
    const flatpickr = require('flatpickr').default;
    const dictionary = require('../dictionary')({
        'cs': require('flatpickr/dist/l10n/cs').default.cs,
    });

    flatpickr.defaultConfig.prevArrow = require('../../images/icon/common/chevron-left.svg');
    flatpickr.defaultConfig.nextArrow = require('../../images/icon/common/chevron-right.svg');

    Alpine.directive('flatpickr', (el, {}, { cleanup }) => {
        const mode = el.getAttribute('data-mode');
        const options = {
            mode: mode ? mode : 'single',
        };

        const translations = dictionary.resolve();

        if (null !== translations) {
            options['locale'] = translations;
        }

        if ('range' === mode && el.hasAttribute('data-default-date')) {
            options['defaultDate'] = JSON.parse(el.getAttribute('data-default-date'));
            el.removeAttribute('data-default-date');
        }

        const fp = flatpickr(el, options);

        cleanup(() => {
            fp.destroy();
        });
    });
}

export default Flatpickr;
