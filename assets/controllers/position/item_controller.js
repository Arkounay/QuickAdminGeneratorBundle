import { Controller } from 'stimulus';

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
        if (this.currentPosition === this.defaultPosition) {
            this.buttonTarget.classList.add('invisible');
        } else {
            this.buttonTarget.classList.remove('invisible')
        }
    }

    resetPosition() {
        this.inputTarget.value = this.defaultPosition;
        this.changePosition();
    }

    get currentPosition() {
        return parseInt(this.inputTarget.value);
    }

}
