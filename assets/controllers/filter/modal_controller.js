import {Controller} from '@hotwired/stimulus';
import Modal from 'bootstrap/js/src/modal';

export default class extends Controller {

    connect() {
        this.filterElement = document.querySelector('#filter-modal');
        this.filterModal = new Modal(this.filterElement);
        this.filterForm = this.filterElement.querySelector('#filter-form');
        this.ajaxFilterUrl = this.element.dataset.ajaxRoute;

        if (this.filterForm.querySelectorAll('.is-invalid').length) {
            setTimeout(() => this.open(null), 0)
        }

        this.filterElement.querySelector('form').addEventListener('submit', () => {
            this.filterModal.hide();
        })
    }

    open(event) {
        if (event !== null) {
            event.preventDefault();
        }
        this.filterModal.show();
        if (this.filterForm.innerHTML.trim() === '') {
            fetch(this.ajaxFilterUrl)
                .then((response) => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error('An error occurred.');
                    }
                })
                .then((html) => {
                    this.filterForm.innerHTML = html;
                    this.filterForm.dispatchEvent(new Event('filter_shown'));
                })
                .catch((error) => {
                    alert(error.message);
                });
        } else {
            this.filterElement.dispatchEvent(new Event('filter_shown'));
        }
    }

}
