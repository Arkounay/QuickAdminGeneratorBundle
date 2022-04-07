import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.element.setAttribute("style", "height:" + (this.element.scrollHeight) + "px;overflow-y:hidden;");
        this.element.addEventListener('input', this.onInput, false);
    }

    onInput() {
        this.style.height = "auto";
        this.style.height = (this.scrollHeight) + "px";
    }

    disconnect() {
        this.element.removeEventListener('input', this.onInput, false);
    }

}