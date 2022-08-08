'use strict';

const Bridge = require('./modal-bridge');

(function () {
    const payloadElementId = '#modals-payload';

    const Modals = (function () {
        const opened = {};

        Bridge.addListener('init', function (modal) {
            if (!opened.hasOwnProperty(modal.name)) {
                return;
            }

            opened[modal.name].modal = modal;
            modal.open();
        });

        Bridge.addListener('opened', function (modal) {
            // update url

            // fix autosize in modals
            modal.$el.querySelectorAll('[x-autosize]').forEach(el => {
                el.refreshAutosize && el.refreshAutosize();
            });
        });

        Bridge.addListener('closed', function (modal) {
            if (!opened.hasOwnProperty(modal.name)) {
                return;
            }

            const data = opened[modal.name];

            modal.destroy();
            data.wrapper.remove();

            delete opened[data.name];
            // update url
        });

        function closeAll() {
            for (let i in opened) {
                if (opened[i].modal) {
                    opened[i].modal.close();
                }
            }
        }

        return {
            open(name, html, metadata) {
                const data = {
                    name: name,
                    metadata: metadata,
                    modal: undefined,
                };

                const wrapper = document.createElement('div');
                wrapper.insertAdjacentHTML('afterbegin', html);

                data.wrapper = wrapper;
                opened[name] = data;

                document.body.appendChild(wrapper);

                return data;
            },
            close(names) {
                for (let i in names) {
                    const name = names[i];

                    if ('*' === name) {
                        closeAll();

                        return;
                    }

                    if (opened.hasOwnProperty(name) && opened[name].modal) {
                        opened[name].modal.close();
                    }
                }
            }
        };
    })();

    function initModals(payload) {
        if (payload.hasOwnProperty('modals_to_hide')) {
            Modals.close(payload.modals_to_hide);
        }

        if (payload.hasOwnProperty('modals_to_show')) {
            for (let name in payload.modals_to_show) {
                if (!payload.modals_to_show.hasOwnProperty(name)) {
                    continue;
                }

                const modalDef = payload.modals_to_show[name];
                const data = Modals.open(name, modalDef.content, modalDef.metadata);

                data.wrapper.querySelectorAll('form').forEach(function (form) {
                    window.Nette.initForm(form);
                });
            }
        }
    }

    $(function () {
        const payload = $(payloadElementId).data('payload');

        initModals(payload || {});
    });

    $.nette.ext('plugin-modals', {
        init: function () {
            this.ext('snippets', true).after(function ($el) {
                $el.find(payloadElementId).each(function () {
                    const payload = $(this).data('payload');

                    initModals(payload || {});
                });
            });
        }
    });
})();
