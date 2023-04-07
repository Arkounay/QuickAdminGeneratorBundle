import {Controller} from '@hotwired/stimulus';
import Tooltip from 'bootstrap/js/src/tooltip';

export default class extends Controller {

    connect() {
        new Tooltip(this.element)
    }

}
