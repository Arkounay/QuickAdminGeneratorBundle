import { Controller } from '@hotwired/stimulus';
import Modal from 'bootstrap/js/src/modal';

export default class extends Controller {

    static values = {
        title: String,
        ajaxTarget: String,
        html: String,
        hasUpperRightCloseButton: Boolean,
        hasSaveButton: Boolean,
        saveButtonLabel: String,
        saveButtonClass: String,
        hasCloseButton: Boolean,
        closeButtonLabel: String,
        closeButtonClass: String,
        classes: String,
        backdrop: {type: String, default: 'true'},
        focus: Boolean,
        keyboard: Boolean
    }

    connect() {
        this.element.addEventListener('click', this.open);
    }

    disconnect() {
        this.element.removeEventListener('click', this.open)
    }

    open = (e) => {
        e.preventDefault();

        const upperRightClose = (!this.hasHasUpperRightCloseButtonValue || this.hasUpperRightCloseButtonValue) ? `<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="${this.closeButtonLabelValue}"></button>` : '';
        const closeButton = (this.hasHasCloseButtonValue || this.hasCloseButtonValue) ? `<button type="button" class="btn ${this.closeButtonClassValue}" data-bs-dismiss="modal">${this.closeButtonLabelValue}</button>` : '';
        const saveButton = this.hasSaveButtonValue ? `<button type="button" class="btn-save btn ${this.saveButtonClassValue}">${this.saveButtonLabelValue}</button>` : '';
        const footer = (closeButton || saveButton) ? `<div class="modal-footer"> ${closeButton} ${saveButton} </div>` : '';

        const modalHtml = `
        <div class="modal fade" id="qag-generic-modal" tabindex="-1" role="dialog">
          <div class="modal-dialog ${this.classesValue}">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">${this.titleValue}</h5>
                ${upperRightClose}
              </div>
              <div class="modal-body">
                ${this.htmlValue}
              </div>
              ${footer}
            </div>
          </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modalNode = document.querySelector('#qag-generic-modal');

        if (this.hasAjaxTargetValue && this.ajaxTargetValue) {
            fetch(this.ajaxTargetValue)
                .then((response) => response.text())
                .then((html) => {
                    modalNode.querySelector('.modal-body').innerHTML = html;
                });
        }

        if (saveButton) {
            modalNode.querySelector('.btn-save').addEventListener('click', () => {
                const form = modalNode.querySelector('form');
                if (form) {
                    form.submit();
                } else {
                    console.error('Form not found in modal');
                }
            });
        }

        let backdrop = this.backdropValue;
        if (backdrop === 'true') {
            backdrop = true;
        } else if (backdrop === 'false') {
            backdrop = false;
        }
        const modal = new Modal('#qag-generic-modal', {
            backdrop: backdrop,
            focus: this.focusValue,
            keyboard: this.keyboardValue,
        });
        modal.show();

        modalNode.addEventListener('hidden.bs.modal', event => {
            modal.dispose();
            modalNode.remove();
        })
    }

}
