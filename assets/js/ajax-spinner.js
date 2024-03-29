'use strict';

(function () {
    const showed = [];

    const findSpinner = el => {
        if ('self' === el.data('spinner-for')) {
            return el;
        }

        const id = el.attr('id');

        if (!id) {
            return null;
        }

        const spinner = $('[data-spinner-for~="' + id + '"]');

        return spinner.length ? spinner : null;
    };

    const show = spinner => {
        spinner && spinner.addClass('spinner') && showed.push(spinner);
    };

    const hide = spinner => {
        if (!spinner) {
            return;
        }

        spinner.removeClass('spinner');

        for (let i in showed) {
            if (showed[i] === spinner) {
                showed.splice(i, 1);
            }
        }
    };

    $.nette.ext('plugin-spinner', {
        start: function (jqXHR, settings) {
            show(this.findSpinnerElement(settings));
        },
        complete: function (jqXHR, status, settings) {
            hide(this.findSpinnerElement(settings));
        }
    }, {
        findSpinnerElement: function (settings) {
            if (!settings.hasOwnProperty('nette')) {
                return null;
            }

            let spinner = null;

            if (settings.nette.ui) {
                spinner = findSpinner($(settings.nette.ui));
            }

            if (!spinner && settings.nette.form) {
                spinner = findSpinner(settings.nette.form);
            }

            return spinner;
        }
    });

    $(document).on('submit', 'form:not(.ajax)', function () {
        show(findSpinner($(this)));
    });

    $(document).on('click', 'a:not(.ajax)', function () {
        show(findSpinner($(this)));
    });

    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            for (let i in showed) {
                hide(showed[i]);
            }
        }
    });
})();
