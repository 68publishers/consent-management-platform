'use strict';

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

        const options = {
            mode: expression,
            indentWithTabs: true,
            indentUnit: 4,
            showTrailingSpace: true,
            viewportMargin: Infinity,
            autoRefresh: true,
            lineNumbers: -1 !== modifiers.indexOf('linenumbers'),
            readOnly: -1 !== modifiers.indexOf('readonly'),
            lineWrapping: -1 !== modifiers.indexOf('wrap'),
        };

        if ('json' === expression) {
            options.mode = {
                name: 'javascript',
                json: true,
            }

            if (modifiers.indexOf('pretty')) {
                try {
                    el.innerHTML = JSON.stringify(JSON.parse(el.innerText), null, 4);
                } catch (err) {
                    // ignore
                }
            }
        }

        const editor = codemirror.fromTextArea(el, options);

        el.CodeMirror = editor;

        editor.on('change', function() {
            editor.save();
        });
    });
}

export default Codemirror;
