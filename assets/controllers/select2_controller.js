import { Controller } from 'stimulus';
import 'select2';

export default class extends Controller {

    connect() {
        this.select.select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }

    disconnect() {
        this.select.select2('destroy');
    }

    get select() {
        return $(this.element);
    }

}
