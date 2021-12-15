import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLInputElement} selectAllCheckboxTarget
 * @property {HTMLButtonElement[]} rowCheckboxTargets
 * @property {HTMLInputElement} actionsContainerTarget
 */
export default class extends Controller {

    static targets = ['selectAllCheckbox', 'rowCheckbox', 'actionsContainer']

    selectAll(event) {
        let allAreSelected = this.allAreSelected;

        const checked = event.target.checked;
        if (checked && !allAreSelected) {
            this.rowCheckboxTargets.forEach(el => el.checked = true);
        } else if (!checked && allAreSelected) {
            this.rowCheckboxTargets.forEach(el => el.checked = false);
        }
        this.updateActionContainerVisibility();
    }

    selectOne(event) {
        this.selectAllCheckboxTarget.checked = this.allAreSelected;
        this.updateActionContainerVisibility();
    }

    updateActionContainerVisibility() {
        let hasAtLeastOneSelected = false;
        this.rowCheckboxTargets.forEach((el) => {
            if (el.checked) {
                hasAtLeastOneSelected = true;
                return false;
            }
        });

        if (hasAtLeastOneSelected) {
            this.actionsContainerTarget.style.display = '';
        } else {
            this.actionsContainerTarget.style.display = 'none';
        }
    }

    get allAreSelected() {
        let res = true;
        this.rowCheckboxTargets.forEach((el) => {
            if (!el.checked) {
                res = false;
                return false;
            }
        });

        return res;
    }

}
