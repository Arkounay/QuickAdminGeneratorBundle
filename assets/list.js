$(() => {
    const $deleteModal = $('#delete-modal');
    $('.js-delete-item').click(function (e) {
        e.preventDefault();
        const name = $(this).data('name');
        $deleteModal.find('.js-entity-to-string').text(name);
        $deleteModal.modal('show');
        $deleteModal.find('form').attr('action', $(this).attr('href'));
    })


    const $batchSelectAll = $('#batch-actions-select-all');
    const $batchRow = $('.batch-action');
    const $batchActionsContainer = $('.batch-actions-container')

    $batchSelectAll.change(function () {
        let allAreSelected = true;
        $batchRow.each(function (i, el) {
            if (!$(el).is(':checked')) {
                allAreSelected = false;
                return false;
            }
        });

        const checked = $(this).is(':checked');
        if (checked && !allAreSelected) {
            $batchRow.prop('checked', true);
        } else if (!checked && allAreSelected) {
            $batchRow.prop('checked', false);
        }
        updateDisplayBatchActions();
    });
    $batchRow.change(function () {
        if ($batchSelectAll.is(':checked')) {
            $batchSelectAll.prop('checked', false);
        }
        updateDisplayBatchActions();
    });

    function updateDisplayBatchActions() {
        let hasAtLeastOneSelected = false;
        $batchRow.each(function (i, el) {
            if ($(el).is(':checked')) {
                hasAtLeastOneSelected = true;
                return false;
            }
        });
        $batchActionsContainer.toggle(hasAtLeastOneSelected);
    }

    $('.batch-form-button').click(function () {
        $('#batch-form').attr('action', $(this).data('action'))
    })
    const $batchDeleteModal = $('#batch-delete-modal');
    $('.js-delete-items').click(function (e) {
        e.preventDefault();
        $batchDeleteModal.modal('show');
    })


    // filter
    const $filterModal = $('#filter-modal');
    const $filterForm = $('#filter-form');
    $('.js-filter').click(function (e) {
        e.preventDefault();
        $filterModal.modal('show');
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
    });
    $filterModal.find('.btn-submit').click(function () {
        $filterModal.find('form').submit();
    })

    let filterFormInit = false;
    $filterForm.on('filter_shown', function () {
        if (!filterFormInit) {
            filterFormInit = true;

            // numbers and dates
            $('.filter-row .filter-choice').change(function () {
                console.log("hi");
                const $this = $(this);
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
            }).change();
        }
    });

})