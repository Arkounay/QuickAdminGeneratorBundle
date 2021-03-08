import { Controller } from 'stimulus';
import 'select2';

export default class extends Controller {
    $deleteModal;
    $filterForm;

    connect() {
        this.$deleteModal = $('#filter-modal');
        this.$filterForm = this.$deleteModal.find('#filter-form');
    }

    open(event) {
        event.preventDefault();
        const $filterForm = this.$filterForm;
        this.$deleteModal.modal('show');
        if ($filterForm.html().trim() === '') {
            $.ajax({
                url: ajaxFilterUrl,
                type: "GET",
                success: function (res) {
                    $filterForm.html(res);
                    $filterForm.trigger('filter_shown');
                },
                error: function (res) {
                    alert('An error occurred.');
                }
            });
        } else {
            $filterForm.trigger('filter_shown');
        }
    }

}
