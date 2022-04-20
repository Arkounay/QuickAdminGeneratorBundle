import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        // fix bs dropdown in responsive table stuck inside its container
        this.element.addEventListener('show.bs.dropdown', (e) => {
            const dropdown = e.target.parentNode.querySelector('.dropdown-menu');
            if (dropdown !== null) {
                document.body.appendChild(dropdown);
            }
        })
    }

}
