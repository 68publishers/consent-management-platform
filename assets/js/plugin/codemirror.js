function Codemirror(Alpine) {
    const codemirror = require('codemirror');

    require('codemirror/mode/htmlmixed/htmlmixed');
    require('codemirror/mode/javascript/javascript');
    require('codemirror/addon/display/autorefresh');

    Alpine.directive('codemirror', (el, {modifiers, expression}, {}) => {
        if ('textarea' !== el.nodeName.toLowerCase()) {
            console.warn('Codemirror can be initialized only with <textarea> element.');

            return;
        }

        const editor = codemirror.fromTextArea(el, {
            mode: expression,
            indentWithTabs: true,
            indentUnit: 4,
            showTrailingSpace: true,
            viewportMargin: Infinity,
            autoRefresh: true,
            readOnly: -1 !== modifiers.indexOf('readonly'),
            lineWrapping: -1 !== modifiers.indexOf('wrap'),
        });

        editor.on('change', function() {
            editor.save();
        });
    });
}

export default Codemirror;
