import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['watched']

    connect() {
        this.observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                this.#checkIsFistElementIsActive(mutation.target);
            });
        });
        this.observer.observe(this.watchedTarget, { attributes: true });
        this.#checkIsFistElementIsActive(this.watchedTarget);
    }

    #checkIsFistElementIsActive(target) {
        if (target.classList.contains('active')) {
            this.element.classList.add('first-element-active');
        } else {
            this.element.classList.remove('first-element-active');
        }
    }

    disconnect() {
        this.observer.disconnect();
    }

}