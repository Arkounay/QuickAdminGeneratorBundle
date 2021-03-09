import { Controller } from 'stimulus';
import 'select2';

export default class extends Controller {

    connect() {
        this.$switch = $(this.element);
    }

    toggle() {
        const $switch = this.$switch;
        if (!$switch.data('changing')) {
            $switch.data('changing', true);

            $.ajax({
                url: $switch.data('url'),
                type: 'POST',
                data: {index: $switch.data('index'), checked: $switch.is(':checked')},
                success: function () {
                    $switch.data('changing', false);
                },
                error: function () {
                    console.log("An error occurred");
                    $switch.click();
                }
            });
        }
    }

}
