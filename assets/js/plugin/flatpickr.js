function Flatpickr(Alpine) {
    const flatpickr = require('flatpickr').default;
    const dictionary = require('../dictionary')({
        'cs': require('flatpickr/dist/l10n/cs').default.cs,
    });

    flatpickr.defaultConfig.prevArrow = `
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>
    `;
    flatpickr.defaultConfig.nextArrow = `
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
    `;

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
