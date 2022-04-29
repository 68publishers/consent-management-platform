'use strict';

import 'script-loader!live-form-validation';

(function () {
    /**
     * LiveForm Options
     */
    LiveForm.setOptions({
        showMessageClassOnParent: false,
        controlErrorClass: 'border-red-500 rounded',
        showValid: false,
        messageErrorClass: 'text-red-500 text-xs italic pt-2',
        messageTag: 'div',
        messageErrorPrefix: '',
        messageParentClass: 'lfv-message-parent'
    });
})();
