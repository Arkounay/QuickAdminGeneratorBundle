import './styles/app.scss';

import './bootstrap';
import 'jquery';
import 'bootstrap';
import * as Turbo from "@hotwired/turbo"
import Tooltip from 'bootstrap/js/src/tooltip';

global.$ = global.jQuery = $;

function applyTooltip() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map((tooltipTriggerEl) => {
        return new Tooltip(tooltipTriggerEl)
    })
}

applyTooltip();

document.addEventListener('turbo:load', () => applyTooltip());