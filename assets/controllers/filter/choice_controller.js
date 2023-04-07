import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.change();
    }

    change(event) {
        const parent = this.element.closest('.filter-row');
        const multiples = parent.querySelectorAll('.js-filter-multiple');
        const singles = parent.querySelectorAll('.js-filter-single');
        switch (this.element.value) {
            case '=':
            case '!=':
            case '<':
            case '>':
                this.#toggleMultiple(multiples, singles, false)
                break;
            default:
                this.#toggleMultiple(multiples, singles, true)
        }
    }

    #toggleMultiple(multiples, singles, val) {
        multiples.forEach((i) => {
            i.hidden = !val;
        })
        singles.forEach((i) => {
            i.hidden = val;
        })
    }


}
