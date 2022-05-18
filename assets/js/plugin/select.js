'use strict';

function Select(Alpine) {

    Alpine.data('select', () => ({
        selectEl: null,
        opened: false,
        multiple: false,
        activeIndex: null,
        activeDescendant: null,
        selected: [],
        options: [],

        selectedText() {
            const labels = [];

            for (let i in this.selected) {
                labels.push(this.options[this.selected[i]].label);
            }

            return labels.join(', ');
        },

        isSelected(index) {
            return -1 !== this.selected.indexOf(index);
        },

        toggle() {
            if (this.opened) {
                return this.close();
            }

            this.$refs.button.focus();
            this.opened = true;

            if (this.multiple) {
                return;
            }

            this.activeIndex = this.selected.length ? this.selected.slice(0, 1)[0] : null;

            this.$nextTick((() => {
                this.$refs.options.focus();
                this.$refs.options.children[this.activeIndex].scrollIntoView({
                    block: 'nearest'
                })
            }));
        },

        close(focusAfter) {
            if (!this.opened) {
                return;
            }

            this.opened = false;

            focusAfter && focusAfter.focus();
        },

        active() {
            return this.options[this.activeIndex];
        },

        choose(index) {
            if (!this.multiple) {
                this.selected = [index];
                this.updateSelectValue();
                this.close();

                return;
            }

            const selected = this.selected;
            const indexOfIndex = selected.indexOf(index);

            if (-1 === indexOfIndex) {
                selected.push(index);
            } else {
                delete selected[indexOfIndex];
            }

            this.selected = selected.sort();
            this.updateSelectValue();
        },

        updateSelectValue() {
            const values = [];

            for (let i in this.selected) {
                values.push(this.options[this.selected[i]].value);
            }

            for (let opt of this.selectEl.options) {
                opt.selected = -1 !== values.indexOf(opt.value);
            }

            //this.selectEl.dispatchEvent(new Event('change'));
            $(this.selectEl).trigger('change');
        },

        select: {
            ['x-init']() {
                const select = this.$el.nextElementSibling;

                if (!select) {
                    return;
                }

                this.selectEl = select;
                this.multiple = select.multiple || false;

                for (let opt of select.options) {
                    const option = {
                        value: opt.value,
                        label: opt.innerText,
                    };

                    this.options.push(option);

                    if (opt.selected) {
                        this.selected.push(this.options.length -1);
                    }
                }

                this.$watch('activeIndex', (() => {
                    this.opened && (null !== this.activeIndex ? this.activeDescendant = this.$refs.options.children[this.activeIndex].id : this.activeDescendant = '')
                }))
            },
            ['x-on:keydown.escape.prevent.stop']() {
                this.close(this.$refs.button);
            },
            ['x-on:focusin.window']() {
                !this.$refs.options.contains(this.$event.target) && this.close();
            },
            ['x-id']: '["select"]',
        },

        selectButton: {
            ['x-ref']: 'button',
            ['x-on:click']() {
                this.toggle();
            },
            [':aria-haspopup']() {
                return 'listbox';
            },
            [':aria-expanded']() {
                return this.opened;
            }
        },

        selectOptions: {
            ['x-transition:enter']: '',
            ['x-transition:enter-start']: '',
            ['x-transition:enter-end']: '',
            ['x-transition:leave']: 'transition ease-in duration-100',
            ['x-transition:leave-start']: 'opacity-100',
            ['x-transition:leave-end']: 'opacity-0',
            ['x-on:click.outside']() {
                this.close(this.$refs.button);
            },
            [':aria-activedescendant']() {
                return this.activeDescendant;
            },
            ['x-show']() {
                return this.opened;
            },
            ['x-ref']: 'options',
            [':id']() {
                return this.$id('select');
            },
        },
    }));

    Alpine.directive('select', (el, {}, { cleanup }) => {
        const selectHtml = `
            <div x-data="select" x-bind="select" class="relative">
                <button x-bind="selectButton" type="button" class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <span class="block truncate" x-html="selectedText() || '&nbsp'"></span>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>

                <ul x-bind="selectOptions" class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" tabindex="-1" role="listbox">
                    <template x-for="(option, index) in options" :key="option.value">
                        <li x-on:click="choose(index)" x-on:mouseenter="activeIndex = index" x-on:mouseleave="activeIndex = null" :id="$id('select') + '-option-' + index" class="cursor-default select-none relative py-2 pl-3 pr-9" :class="{'text-white': activeIndex === index, 'text-gray-900': activeIndex !== index, 'bg-indigo-600': activeIndex === index}" role="option">
                            <span x-text="option.label" class="text-left font-normal block truncate"></span>

                            <span x-show="isSelected(index)" class="absolute inset-y-0 right-0 flex items-center pr-2 text-indigo-600" :class="{'text-white': activeIndex === index, 'text-indigo-600': activeIndex !== index}">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </li>
                    </template>
                </ul>
            </div>
        `;

        const originalDisplay = el.style.display;
        el.style.display = 'none';

        el.insertAdjacentHTML('beforebegin', selectHtml);

        cleanup(() => {
            const component = el.previousElementSibling;

            if (component && 'select' === component.getAttribute('x-data')) {
                component.remove();
                el.style.display = originalDisplay;
            }
        });
    });
}

export default Select;
