import './styles/app.scss';

import './bootstrap';

import 'jquery';
import 'bootstrap';
import './forms/collection';
import './list';

global.$ = global.jQuery = $;

// tooltips
$('[data-toggle="tooltip"]').tooltip();