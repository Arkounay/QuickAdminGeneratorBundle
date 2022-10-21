import { Controller } from '@hotwired/stimulus';
import 'select2';

export default class extends Controller {

    connect() {
        this.select.select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        this.select.on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        })
    }

    disconnect() {
        this.select.select2('destroy');
    }

    get select() {
        return $(this.element);
    }

}
