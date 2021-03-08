import { Controller } from 'stimulus';
import 'select2';

export default class extends Controller {

    connect() {
        this.change();
    }

    change(event) {
        const $this = $(this.element);
        const $parent = $this.closest('.filter-row');
        const $multiple = $parent.find('.js-filter-multiple');
        const $single = $parent.find('.js-filter-single');
        switch ($this.val()) {
            case '=':
            case '!=':
            case '<':
            case '>':
                $multiple.hide();
                $single.show();
                break;
            default:
                $multiple.show();
                $single.hide();
        }
    }


}
