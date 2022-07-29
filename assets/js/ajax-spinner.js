'use strict';

(function () {
    $.nette.ext('plugin-spinner', {
        start: function (jqXHR, settings) {
            const el = this.findSpinnerElement(settings);

            if (null !== el) {
                el.addClass('spinner');
            }
        },
        complete: function (jqXHR, status, settings) {
            const el = this.findSpinnerElement(settings);

            if (null !== el) {
                el.removeClass('spinner');
            }
        }
    }, {
        findSpinnerElement: function (settings) {
            if (!settings.hasOwnProperty('nette')) {
                return null;
            }

            let spinner;

            if (settings.nette.ui) {
                const ui = $(settings.nette.ui);

                if ('self' === ui.data('spinner-for')) {
                    return $(settings.nette.ui);
                }

                if (ui.attr('id')) {
                    spinner = $('[data-spinner-for="' + ui.attr('id') + '"]');

                    if (spinner.length) {
                        return spinner;
                    }
                }
            }

            if (settings.nette.form && settings.nette.form.attr('id')) {
                spinner = $('[data-spinner-for="' + settings.nette.form.attr('id') + '"]');

                if (spinner.length) {
                    return spinner;
                }
            }

            return null;
        }
    });
})();
