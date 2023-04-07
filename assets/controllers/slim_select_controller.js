import {Controller} from '@hotwired/stimulus';
import SlimSelect from "slim-select";

export default class extends Controller {

    static values = {
        searchText: String,
        searchPlaceholder: String,
        maxValuesMessage: String,
        required: Boolean
    }

    connect() {
        this.select = new SlimSelect({
            select: this.element,
            settings: {
                allowDeselect: !this.requiredValue,
                placeholderText: '',
                searchText: this.searchTextValue,
                searchPlaceholder: this.searchPlaceholderValue,
                closeOnSelect: !this.element.multiple,
                maxValuesShown: 2,
                maxValuesMessage: this.maxValuesMessageValue,
            }
        })
    }

    disconnect() {
        this.select.destroy();
    }

}
