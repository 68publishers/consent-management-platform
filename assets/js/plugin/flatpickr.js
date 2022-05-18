function Flatpickr(Alpine) {
    const flatpickr = require('flatpickr').default;
    const dictionary = require('../dictionary')({
        'cs': require('flatpickr/dist/l10n/cs').default.cs,
    });

    Alpine.directive('flatpickr', (el, {}, { cleanup }) => {
        const mode = el.getAttribute('flatpickr-mode');
        const options = {
            mode: mode ? mode : 'single',
        };

        const translations = dictionary.resolve();

        if (null !== translations) {
            options['locale'] = translations;
        }

        const fp = flatpickr(el, options);

        cleanup(() => {
            fp.destroy();
        });
    });
}

export default Flatpickr;
