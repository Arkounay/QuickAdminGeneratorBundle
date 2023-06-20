import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        new bootstrap.Tooltip(this.element)
    }

}
