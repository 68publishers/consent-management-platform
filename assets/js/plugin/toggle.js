'use strict';

function Toggle(Alpine) {
    Alpine.data('toggle', () => ({
        inputEl: null,
        checked: false,
        disabled: false,

        doToggle() {
            this.checked = !this.checked;
            this.inputEl.checked = this.checked;

            this.inputEl.dispatchEvent(new Event('change'));
            $(this.inputEl).trigger('change');
        },

        toggle: {
            ['x-init']() {
                const input = this.$el.nextElementSibling;

                if (!input) {
                    return;
                }

                this.inputEl = input;
                this.checked = input.checked;
                this.disabled = input.disabled;

                this.inputEl.addEventListener('change', e => {
                    this.checked = e.target.checked;
                });

                this.$nextTick(() => {
                    this.$el.classList.add('transition-colors', 'ease-in-out', 'duration-200');
                    this.$el.children[0].classList.add('transition', 'ease-in-out', 'duration-200');
                });
            },
            ['x-on:click']() {
                this.doToggle();
            },
            ['x-on:keydown.enter.prevent']() {
                const $form = this.$el.form;

                if (!$form) {
                    return;
                }

                const $submitButton = $form.querySelector('input[type="submit"], button[type="submit"]');

                if ($submitButton) {
                    $submitButton.click();
                }
            },
            [':aria-checked']() {
                return this.checked;
            },
        },
    }));

    Alpine.directive('toggle', (el, {}, { cleanup }) => {
        const disabled = el.disabled;
        const toggleHtml = `
            <button x-data="toggle" x-bind="toggle" :disabled="disabled" :class="{'bg-indigo-600': checked, 'bg-gray-200': !checked, 'cursor-not-allowed': disabled, 'opacity-75': disabled}" type="button" class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" role="switch">
                <span :class="checked ? 'translate-x-5' : 'translate-x-0'" aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0"></span>
            </button>
        `;

        const originalDisplay = el.style.display;
        el.style.display = 'none';

        el.insertAdjacentHTML('beforebegin', toggleHtml);

        cleanup(() => {
            const component = el.previousElementSibling;

            if (component && 'input' === component.getAttribute('x-data')) {
                component.remove();
                el.style.display = originalDisplay;
            }
        });
    });
}

export default Toggle;
