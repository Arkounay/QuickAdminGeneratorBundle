import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.element.addEventListener('shown.bs.dropdown', (e) => {
            // Fix dropdown responsive x scroll bug
            e.target.parentNode.querySelector('.dropdown-menu').style.top = '1px';
        })
    }

}
