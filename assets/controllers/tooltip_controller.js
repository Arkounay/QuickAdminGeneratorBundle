import { Controller } from 'stimulus';
import 'select2';

export default class extends Controller {

    connect() {
        $(this.element).tooltip();
    }

}
