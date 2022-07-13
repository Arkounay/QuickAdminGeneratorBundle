import './styles/app.scss';

import $ from "jquery";
global.$ = global.jQuery = $;

import './bootstrap';
import 'bootstrap';
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
document.addEventListener('turbo:submit-end', () => {
    setTimeout(() => {
        const formErrorMessage = document.querySelector('.form-error-message');

        if (formErrorMessage) {
            window.scroll({
                top: formErrorMessage.getBoundingClientRect().top + window.scrollY,
                behavior: 'auto'
            });
        }
    }, 0);
});
document.addEventListener('turbo:before-cache', () => document.querySelectorAll('.highlighted').forEach((e) => e.classList.remove('highlighted')));