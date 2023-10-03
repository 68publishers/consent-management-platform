import Picker from 'vanilla-picker/csp';

export default function ColorPicker(Alpine) {
    Alpine.directive('color-picker', (el, { modifiers }, { cleanup }) => {
        const input = el.querySelector('input[type="hidden"]');
        const defaultHexValue = input && input.value && '' !== input.value ? input.value : ('value' in el.dataset && '' !== el.dataset.value ? el.dataset.value : undefined);
        let placement;

        if (modifiers.includes('placement')) {
            placement = modifiers[modifiers.indexOf('placement') + 1];
        }

        const options = {
            parent: el,
            popup: placement || 'bottom',
            alpha: false,
            editor: true,
            editorFormat: 'hex',
            defaultColor: '#ffffff',
            onChange: color => {
                el.style.background = color.rgbString;
                input && (input.value = color.printHex(false));
                input && input.dispatchEvent(new Event('change'));
            },
        };

        if (defaultHexValue) {
            options.color = defaultHexValue;
        }

        const picker = new Picker(options);
        el.style.background = defaultHexValue;

        cleanup(() => {
            picker.destroy();
        });
    });
}
