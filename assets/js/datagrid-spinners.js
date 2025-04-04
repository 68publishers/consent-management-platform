'use strict';

(function () {
    $.nette.ext('ublaboo-spinners', {
        before: function(xhr, settings) {
            var el, id, row_detail, spinner_template, grid_fullname;

            if (settings.nette) {
                el = settings.nette.el;
                spinner_template = $('<div class="ublaboo-spinner ublaboo-spinner-small"><i></i><i></i><i></i><i></i></div>');
                if (el.is('.datagrid [name="group_action[submit]"]')) {
                    return el.after(spinner_template);
                } else if (el.is('.datagrid a') && el.data('toggle-detail')) {
                    id = settings.nette.el.attr('data-toggle-detail');
                    grid_fullname = settings.nette.el.attr('data-toggle-detail-grid-fullname');
                    row_detail = $('.item-detail-' + grid_fullname + '-id-' + id);
                    if (!row_detail.hasClass('loaded')) {
                        return el.addClass('ublaboo-spinner-icon');
                    }
                } else if (el.is('.datagrid .grid-pagination a') || el.is('.datagrid .datagrid-per-page-submit') || el.is('.datagrid .reset-filter')) {
                    return el.closest('.row-grid-bottom').find('.col-per-page').prepend(spinner_template);
                } else if (settings.nette.form) {
                    var form = settings.nette.form;
                    var datagrid = form.closest('.datagrid');

                    if (datagrid) {
                        datagrid.find('.row-grid-bottom').find('.col-per-page').prepend(spinner_template);
                    }
                }
            }
        },
        complete: function() {
            $('.ublaboo-spinner').remove();
            return $('.ublaboo-spinner-icon').removeClass('ublaboo-spinner-icon');
        }
    });
})();
