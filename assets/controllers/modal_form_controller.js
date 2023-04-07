import {Controller} from '@hotwired/stimulus';
import Modal from 'bootstrap/js/src/modal';

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
        new Modal(this.targetModal).show()
        this.targetModal.querySelector('form').setAttribute('action', this.element.href);
    }

}
