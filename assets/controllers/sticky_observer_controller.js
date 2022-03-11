import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        activeClass: String
    }

    connect() {
        this.observer = new IntersectionObserver(
            ([e]) => e.target.classList.toggle(this.activeClassValue, e.intersectionRatio < 1),
            { threshold: [1] }
        );

        this.observer.observe(this.element);
    }

    disconnect() {
        this.observer.disconnect();
    }

}
