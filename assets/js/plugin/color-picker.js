import Picker from 'vanilla-picker/csp';

const resolveFontColor = ({r, g, b}) => (0.299 * r + 0.587 * g + 0.114 * b) > 128 ? '#000000' : '#ffffff';

export default function ColorPicker(Alpine) {
    Alpine.directive('color-picker', (el, {}, { cleanup }) => {
        if ('input' !== el.tagName.toLowerCase()) {
            console.warn('Unable to initialize color-picker on the element', el);

            return;
        }

        const wrapper = document.createElement('div');
        const defaultHexValue =  el.value && '' !== el.value ? el.value : '#fff';

        wrapper.style.position = 'relative';

        el.parentNode.insertBefore(wrapper, el);
        wrapper.appendChild(el);

        el.readonly = true;

        let openOnFocus = true;

        const picker = new Picker({
            parent: wrapper,
            popup: 'bottom',
            alpha: false,
            editor: true,
            editorFormat: 'hex',
            color: defaultHexValue,
            onDone: color => {
                const [r, g , b] = color.rgba;

                el.style.background = color.rgbString;
                el.style.color = resolveFontColor({r, g, b});
                el.value = color.printHex(false);

                el.dispatchEvent(new Event('change'));

                openOnFocus = false;
                el.focus();
            }
        });

        const [r, g , b] = picker.color.rgba;

        el.style.background = defaultHexValue;
        el.style.color = resolveFontColor({r, g, b});

        const focusHandler = () => {
            if (!openOnFocus) {
                openOnFocus = true;

                return;
            }

            setTimeout(() => {
                picker.show();
            }, 0);
        };

        el.addEventListener('focus', focusHandler);

        cleanup(() => {
            picker.destroy();
            el.removeEventListener('focus', focusHandler);
        });
    });
}
