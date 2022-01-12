import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    $filterModal;
    $filterForm;
    ajaxFilterUrl;

    connect() {
        this.$filterModal = $('#filter-modal');
        this.$filterForm = this.$filterModal.find('#filter-form');
        this.ajaxFilterUrl = this.element.dataset.ajaxRoute;

        if (this.$filterForm.find('.is-invalid').length) {
            setTimeout(() => this.open(null), 0)
        }

        this.$filterModal.find('form').on('submit', () => {
            this.$filterModal.modal('hide');
        })
    }

    open(event) {
        if (event !== null) {
            event.preventDefault();
        }
        const $filterForm = this.$filterForm;
        this.$filterModal.modal('show');
        if ($filterForm.html().trim() === '') {
            $.ajax({
                url: this.ajaxFilterUrl,
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
