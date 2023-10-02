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
        messageErrorClass: 'block text-red-500 text-xs pt-2',
        messageTag: 'div',
        messageErrorPrefix: '',
        messageParentClass: 'lfv-message-parent'
    });

    LiveForm.setupHandlers = function (el) {
        if (this.hasClass(el, this.options.disableLiveValidationClass))
            return;

        // Check if element was already initialized
        if (el.getAttribute("data-lfv-initialized"))
            return;

        // Remember we initialized this element so we won't do it again
        el.setAttribute('data-lfv-initialized', 'true');

        const handler = function (event) {
            event = event || window.event;
            Nette.validateControl(event.target ? event.target : event.srcElement);
        };

        const self = this;

        undefined === el.dataset.lfvDisableChange && el.addEventListener('change', handler);
        undefined === el.dataset.lfvDisableBlur && el.addEventListener('blur', handler);
        undefined === el.dataset.lfvDisableKeydown && el.addEventListener('keydown', function (event) {
            if (!self.isSpecialKey(event.which) && (self.options.wait === false || self.options.wait >= 200)) {
                // Hide validation span tag.
                self.removeClass(self.getGroupElement(this), self.options.controlErrorClass);
                self.removeClass(self.getGroupElement(this), self.options.controlValidClass);

                const messageEl = self.getMessageElement(this);
                messageEl.innerHTML = '';
                messageEl.className = '';

                // Cancel timeout to run validation handler
                if (self.timeout) {
                    clearTimeout(self.timeout);
                }
            }
        });

        undefined === el.dataset.lfvDisableKeyup && el.addEventListener('keyup', function (event) {
            if (self.options.wait !== false) {
                event = event || window.event;
                if (event.keyCode !== 9) {
                    if (self.timeout) clearTimeout(self.timeout);
                    self.timeout = setTimeout(function () {
                        handler(event);
                    }, self.options.wait);
                }
            }
        });
    };

    /**
     * Custom form multiplier validator - values must be unique across fields with the same name
     */
    Nette.validators['AppWebUiFormValidatorUniqueMultiplierValuesValidator_validate'] = (elem, arg, val) => {
        const match = elem.name.match(/^(?<PREFIX>.+)\[\d+](?<POSTFIX>.+)$/);

        if (!match || !('PREFIX' in match.groups) || !('POSTFIX' in match.groups)) {
            return true;
        }

        const prefix = match.groups.PREFIX + '['
        const postfix = ']' + match.groups.POSTFIX;

        for (let input of elem.form.elements) {
            if (input !== elem && input.name.startsWith(prefix) && input.name.endsWith(postfix) && val === Nette.getValue(input)) {
                return false;
            }
        }

        return true;
    };
})();
