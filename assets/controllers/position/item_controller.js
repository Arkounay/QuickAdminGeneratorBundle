import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLInputElement} inputTarget
 * @property {HTMLButtonElement} buttonTarget
 */
export default class extends Controller {

    static targets = ['input', 'button']
    static values = { defaultPosition: Number }

    connect() {
        this.defaultPosition = this.currentPosition;
        this.inputTarget['itemController'] = this;
    }

    changePosition() {
        const invisible = this.currentPosition === this.defaultPosition;
        this.element.classList.toggle('position-btn-invisible', invisible)
        this.buttonTarget.classList.toggle('invisible', invisible)
    }

    resetPosition() {
        this.inputTarget.value = this.defaultPosition;
        this.changePosition();
    }

    get currentPosition() {
        return parseInt(this.inputTarget.value);
    }

}
