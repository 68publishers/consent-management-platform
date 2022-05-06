'use strict';

require('select2');

(function () {
    const dictionary = require('./dictionary')({
        en: require('select2/src/js/select2/i18n/en'),
        cs: require('select2/src/js/select2/i18n/cs'),
    });

    const Select = function () {
        return {
            init: function(snippet) {
                snippet.find('select').select2({
                    language: dictionary.resolve(),
                    minimumResultsForSearch: Infinity
                });
            }
        };
    }();

    $(function () {
        Select.init($(document));
    });

    $.nette.ext('plugin-select', {
        init: function () {
            this.ext('snippets', true).after(function ($el) {
                Select.init($el);
            });
        }
    });
})();
