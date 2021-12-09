import { Controller } from 'stimulus';

export default class extends Controller {

    static targets = ['check']

    static values = {
        url: String,
        darkMode: Boolean
    }

    toggle(e) {
        console.log(e.target.tagName);
        if (e.target.tagName === 'LABEL') {
            e.preventDefault();
        }
        this.darkModeValue = !this.darkModeValue;

        if (this.darkModeValue) {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }

        fetch(this.urlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: this.darkModeValue ? 'dark' : 'light'
        });
    }

    darkModeValueChanged(value) {
        this.checkTarget.checked = value;
    }

}
