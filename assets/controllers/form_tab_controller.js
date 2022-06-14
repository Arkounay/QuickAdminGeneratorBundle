import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['tab']

    connect() {
        // focus when error is triggered after form submit
        for (let tabTarget of this.tabTargets) {
            if (tabTarget.querySelector('.badge-error')) {
                tabTarget.click();
                break;
            }
        }

        // focus when required attribute triggers
        for (let el of this.element.querySelectorAll('input,textarea,select')) {
            el.addEventListener('invalid', (event) => {
                const tabPane = el.closest('.tab-pane');
                const index = Array.from(tabPane.parentNode.children).indexOf(tabPane);
                this.tabTargets[index].click();
            });
        }

    }


}