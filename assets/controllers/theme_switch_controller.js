import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['check']

    static values = {
        url: String,
        darkMode: Boolean
    }

    connect() {
        this.darkModeValue = document.body.classList.contains('theme-dark');
        this.observer = new MutationObserver((event) => this.darkModeValue = document.body.classList.contains('theme-dark'));
        this.observer.observe(document.body, {
            attributes: true,
            attributeFilter: ['class'],
            childList: false,
            characterData: false
        })
    }

    disconnect() {
        this.observer.disconnect();
    }

    toggle(e) {
        if (e.target.tagName === 'LABEL') {
            e.preventDefault();
        }
        this.darkModeValue = !this.darkModeValue;
    }

    darkModeValueChanged(value) {
        if (value) {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }

        fetch(this.urlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: value ? 'dark' : 'light'
        });
        this.checkTarget.checked = value;
    }

}
