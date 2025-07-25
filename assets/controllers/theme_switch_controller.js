import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['item']

    static values = {
        url: String,
        theme: String,
        checkedIcon: String,
        uncheckedIcon: String,
    }

    isInitialLoad = true;

    connect() {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (this.themeValue === 'auto') {
                this.refreshTheme();
            }
        })
        this.element.classList.remove("dropstart");
        this.element.classList.add("dropdown");

        document.addEventListener('turbo:load', this.handleTurboLoad);
    }

    disconnect() {
        document.removeEventListener('turbo:load', this.handleTurboLoad);
    }

    handleTurboLoad = () => {
        this.isInitialLoad = true;
        this.refreshTheme();
        this.isInitialLoad = false;
    }

    selectTheme(event) {
        this.themeValue = event.params.theme;
    }

    refreshTheme() {
        if (this.themeValue === 'dark' || this.themeValue === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.dataset.bsTheme = 'dark';
        } else {
            delete document.body.dataset.bsTheme;
        }
    }

    themeValueChanged(value) {
        this.refreshTheme();

        if (!document.documentElement.hasAttribute('data-turbo-preview') && !this.isInitialLoad) {
            fetch(this.urlValue, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: value
            });

            const customEvent = new CustomEvent('theme-switch', {
                detail: { message: value }
            });
            document.body.dispatchEvent(customEvent);

            window.Turbo.cache.clear();
        }

        for (const item of this.itemTargets) {
            if (item.dataset.themeSwitchThemeParam === value) {
                item.firstChild.innerHTML = this.checkedIconValue;
            } else {
                item.firstChild.innerHTML = this.uncheckedIconValue;
            }
        }
    }
}
