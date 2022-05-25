'use strict';

function Tags(Alpine) {

    Alpine.data('tags', () => ({
        inputEl: null,
        enabled: true,
        focused: false,
        values: [],
        newTag: '',

        createTag() {
            if (!this.enabled) {
                return;
            }

            const tag = this.newTag.trim();

            if (tag !== '' && -1 === this.values.indexOf(tag)) {
                this.values.push(tag);
                this.updateInputValue();
            }

            this.newTag = '';
        },

        removeTag(value) {
            if (!this.enabled) {
                return;
            }

            this.values = this.values.filter(v => v !== value);

            this.updateInputValue();
        },

        updateInputValue() {
            this.inputEl.value = this.values.join(',');

            //this.inputEl.dispatchEvent(new Event('change'));
            $(this.inputEl).trigger('change');
        },

        tags: {
            ['x-init']() {
                const input = this.$el.nextElementSibling;

                if (!input) {
                    console.warn('Can\'t initialize tags component, the element not found.');

                    return;
                }

                const inputType = input.tagName;

                if ('INPUT' !== inputType && 'TEXTAREA' !== inputType) {
                    console.warn('Can\'t initialize tags component, the element must be <input> or <textarea>.');

                    return;
                }

                const values = input.value;

                this.inputEl = input;
                this.enabled = !(input.disabled);
                this.values = values ? values.split(',').map(val => val.trim()) : [];
            },
        },
    }));

    Alpine.directive('tags', (el, {}, { cleanup }) => {
        const inputType = el.tagName;

        if ('INPUT' !== inputType && 'TEXTAREA' !== inputType) {
            console.warn('Can\'t initialize tags component, the element must be <input> or <textarea>.');

            return;
        }

        const tagsHtml = `
            <div x-data="tags" x-bind="tags" class="tags" :class="{'tags-disabled': !enabled, 'tags-focused': focused}">            
                <div class="tags-inner">
                    <template x-for="value in values" :key="value">
                        <span class="tags-tag">
                            <span x-text="value" class="tags-tag-value"></span>
                            <button x-show="enabled" type="button" class="tags-tag-remove" x-on:click="removeTag(value)">
                                &times;
                            </button>
                        </span>
                    </template>
                    
                    <input x-show="enabled" class="tags-input" placeholder="..." x-model="newTag" x-on:keydown.enter.prevent="createTag()" x-on:focusin="focused = true" x-on:focusout="focused = false">
                    <span x-show="!enabled">&nbsp;</span>
                </div>
            </div>
        `;

        const originalDisplay = el.style.display;
        el.style.display = 'none';

        el.insertAdjacentHTML('beforebegin', tagsHtml);

        cleanup(() => {
            const component = el.previousElementSibling;

            if (component && 'tags' === component.getAttribute('x-data')) {
                component.remove();
                el.style.display = originalDisplay;
            }
        });
    });
}

export default Tags;
