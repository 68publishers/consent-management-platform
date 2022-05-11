'use strict';

const $ = require('jquery');
const autosize = require('autosize').default;

(function () {
    $(function () {
        autosize($('textarea'));

        $.nette.ext('textarea-autoresize', {
            init: function () {
                this.ext('snippets', true).after(function ($el) {
                    autosize($el.find('textarea'));
                });
            }
        });
    });
})();
