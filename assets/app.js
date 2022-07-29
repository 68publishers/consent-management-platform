'use strict';

import 'nette.ajax.js';
import 'ublaboo-datagrid/assets/datagrid';
//import 'ublaboo-datagrid/assets/datagrid-spinners';
import './js/datagrid-spinners';
import 'ublaboo-datagrid/assets/datagrid-instant-url-refresh';

if ('production' === process.env.NODE_ENV) {
    const Pace = require('pace-js');

    Pace.options.ajax.trackMethods = ['GET', 'POST'];
    Pace.options.restartOnRequestAfter = 5;
}

import Toastr from 'toastr';

window.toastr = Toastr;

import './js/ajax-error';
import './js/ajax-spinner';
import './js/alpine';
import './js/live-form-validation';
import './js/modal/modal';

import './css/style.css';
