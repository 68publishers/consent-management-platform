'use strict';

(function () {

    const $ = require('jquery');
    const Toastr = require('toastr');

    /**
     * Show error toaster when Ajax request fails
     */
    $.nette.ext('plugin-ajax-error', {
        error: function (jqXHR, status) {
            if (status === 'error') {
                Toastr.error('Something is wrong. Try refreshing the page or try again later.');
            }
        }
    });
})();
