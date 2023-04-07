import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    toggle() {
        if (this.element.dataset.changing === 'true') {
            return
        }

        this.element.dataset.changing = 'true';

        const data = new URLSearchParams();
        data.append('index', this.element.dataset.index);
        data.append('checked', this.element.checked);

        fetch(this.element.dataset.url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: data
        })
        .then(response => {
            if (!response.ok) {
                console.log("An error occurred");
                this.element.click();
            }
        })
        .catch(error => {
            console.log("An error occurred:", error);
            this.element.click();
        })
        .finally(() => {
            this.element.dataset.changing = 'false';
        });
    }

}
