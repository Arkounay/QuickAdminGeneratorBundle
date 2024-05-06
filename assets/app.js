import './styles/app.scss';
import './bootstrap';

window.bootstrap = require('bootstrap');

function applyTooltip() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map((tooltipTriggerEl) => {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
}

applyTooltip();

let formSubmitted = false;
document.addEventListener('turbo:load', () => applyTooltip());
document.addEventListener('turbo:submit-end', () => formSubmitted = true);
document.addEventListener('turbo:render', (e) => {
    if (formSubmitted) {
        setTimeout(() => {
            const formErrorMessage = document.querySelector('.form-error-message');
            if (formErrorMessage) {
                window.scroll({
                    top: formErrorMessage.getBoundingClientRect().top + window.scrollY,
                    behavior: 'auto'
                });
            }
            formSubmitted = false;
        }, 10);
    }
});

document.addEventListener('turbo:before-cache', () => {
    document.querySelectorAll('.highlighted').forEach((e) => e.classList.remove('highlighted'));
    document.querySelectorAll('.modal-backdrop').forEach((e) => e.remove());
    document.querySelectorAll('.modal').forEach((e) => e.style.display = 'none');
});