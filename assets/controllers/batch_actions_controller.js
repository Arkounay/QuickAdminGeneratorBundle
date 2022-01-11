import { Controller } from '@hotwired/stimulus';

/**
 * @property {HTMLInputElement} selectAllCheckboxTarget
 * @property {HTMLButtonElement[]} rowCheckboxTargets
 * @property {HTMLInputElement} actionsContainerTarget
 * @property {HTMLInputElement} actionText
 */
export default class extends Controller {

    static targets = ['selectAllCheckbox', 'rowCheckbox', 'actionsContainer', 'actionText']

    selectAll(event) {
        let allAreSelected = this.allAreSelected;

        const checked = event.target.checked;
        if (checked && !allAreSelected) {
            this.rowCheckboxTargets.forEach(el => {
                el.checked = true;
                this.updateRowHighlighting(el);
            });
        } else if (!checked && allAreSelected) {
            this.rowCheckboxTargets.forEach(el => {
                el.checked = false;
                this.updateRowHighlighting(el);
            });
        }
        this.updateActionContainerVisibility();
    }

    selectOne(event) {
        this.updateRowHighlighting(event.target);
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
            let count = 0;
            this.rowCheckboxTargets.forEach((el) => {
                if (el.checked) {
                    count++;
                }
            });
            let text = this.actionTextTarget.dataset.singleCount;
            if (count > 1) {
                text = this.actionTextTarget.dataset.pluralCount.replace('%count%', count);
            }
            this.actionTextTarget.innerHTML = text;
        } else {
            this.actionsContainerTarget.style.display = 'none';
        }
    }

    updateRowHighlighting(checkbox) {
        if (checkbox.checked) {
            checkbox.closest('tr').classList.add('batch-highlighted');
        } else {
            checkbox.closest('tr').classList.remove('batch-highlighted');
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
