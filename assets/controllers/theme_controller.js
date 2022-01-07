import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        theme: String,
        watchForChange: Boolean
    }

    connect() {
        if (this.themeValue === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            this.element.classList.add('theme-dark');
        }
        if (this.watchForChangeValue) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                if (event.matches) {
                    this.element.classList.add('theme-dark');
                } else {
                    this.element.classList.remove('theme-dark');
                }
            })
        }
    }

}
