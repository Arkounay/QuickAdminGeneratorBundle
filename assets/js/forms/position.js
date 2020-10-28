$(() => {
    const $formPositionsSaveButton = $('.form-position button');

    $(document).on('change', '.form-position input', function () {
        const prev = $(this).data('val');
        const current = parseInt($(this).val());

        $formPositionsSaveButton.addClass('invisible');

        if (prev !== current) {
            $(this).parent().find('button').removeClass('invisible');
        }
    });
});
