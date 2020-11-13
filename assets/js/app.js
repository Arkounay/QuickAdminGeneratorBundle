import '../css/app.scss';

import 'jquery';
import 'bootstrap';
import './forms/select2';
import './forms/collection';
import './forms/position';
import './list';

global.$ = global.jQuery = $;

// tooltips
$('[data-toggle="tooltip"]').tooltip();