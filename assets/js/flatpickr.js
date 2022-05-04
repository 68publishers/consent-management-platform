require('flatpickr');

(function () {

    const Dictionary = require('./dictionary')({
        'cs': require('flatpickr/dist/l10n/cs').default.cs,
    });

    const Flatpickr = (function (dictionary) {
        return {
            run: function(snippet) {
                const $flatpickr = snippet.find('[data-toggle="flatpickr"]');

                function init($this) {
                    const options = {
                        mode: ( $this.data('flatpickr-mode') !== undefined ) ? $this.data('flatpickr-mode') : 'single'
                    };

                    /** Fix @see https://github.com/flatpickr/flatpickr/issues/1241 **/
                    if ($this.closest('.modal').length) {
                        options['static'] = true;
                    }

                    const dict = dictionary.resolve();

                    if (null !== dict) {
                        options['locale'] = dict;
                    }

                    $this.flatpickr(options);
                }

                if ($flatpickr.length) {
                    $flatpickr.each(function() {
                        init($(this));
                    });
                }
            }
        };
    }(Dictionary));

    $(function () {
        Flatpickr.run($(document));
    });

    $.nette.ext('plugin-flatpickr', {
        init: function () {
            this.ext('snippets', true).after(function ($el) {
                Flatpickr.run($el);
            });
        }
    });
})();
