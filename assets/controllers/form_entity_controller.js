import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['submit']

    static values = {
        isSubmitting: Boolean,
    }

    connect() {
        this.isSubmittingValue = false;
    }

    onSubmit(e) {
        if (this.isSubmittingValue) {
            e.preventDefault();
        }
        this.isSubmittingValue = true;
    }


}