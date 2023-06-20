import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.targetModal = document.querySelector(this.element.dataset.target);
    }

    open(event) {
        event.preventDefault();
        const name = this.element.dataset.name;
        if (name) {
            this.targetModal.querySelector('.js-entity-to-string').textContent = name;
        }
        new bootstrap.Modal(this.targetModal).show()
        this.targetModal.querySelector('form').setAttribute('action', this.element.href);
    }

}
