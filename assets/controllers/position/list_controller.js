import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLInputElement[]} inputTargets
 */
export default class extends Controller {

    static targets = ['input']

    changePosition(event) {
        this.inputTargets.forEach((element) => {
            if (element !== event.target) {
                element['itemController'].resetPosition();
            }
        })
    }

}
