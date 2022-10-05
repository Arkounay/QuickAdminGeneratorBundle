import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    // fix bs dropdown in responsive table stuck inside its container
    connect() {
        const id = this.element.closest('tr[data-id]').dataset.id;
        let previousDropdown = document.querySelector('.dropdown-menu[data-entity-id="' + id + '"]')
        if (previousDropdown !== null) {
            this.element.parentNode.appendChild(previousDropdown);
        }

        this.element.addEventListener('show.bs.dropdown', (e) => {
            const dropdown = e.target.parentNode.querySelector('.dropdown-menu');
            if (dropdown !== null) {
                dropdown.dataset.entityId = id;
                document.body.appendChild(dropdown);
            }
        })
    }

}
