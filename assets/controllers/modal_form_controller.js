import { Controller } from 'stimulus';
import 'select2';

export default class extends Controller {

    connect() {
        this.$targetModal = $(this.element.dataset.target);
    }

    open(event) {
        event.preventDefault();
        const name = this.element.dataset.name;
        if (name) {
            this.$targetModal.find('.js-entity-to-string').text(name);
        }
        this.$targetModal.modal('show');
        this.$targetModal.find('form').attr('action', this.element.href);
    }

}
