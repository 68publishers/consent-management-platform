'use strict';

import Toastr from 'toastr';

(function () {
    window.toastr = Toastr;
    window.toastr.options.closeButton = true;
    window.toastr.options.closeHtml = `
        <button type="button" class="bg-white rounded-md inline-flex text-gray-500 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-400">
            <span class="sr-only">Close</span>
            <span class="h-5 w-5 text-gray-500">${require('../images/icon/common/x-mark.svg')}</span>
        </button>
    `;
})();
