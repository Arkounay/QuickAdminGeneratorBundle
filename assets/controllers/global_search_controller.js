import { Controller } from 'stimulus';

export default class extends Controller {

    static targets = ['resultBox', 'resultItem'];

    static values = { index: Number }

    connect() {
        const debounce = (func, timeout = 300) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        };

        this.search = debounce(this.search);
    }

    search(e) {
        if (e !== undefined && (e.keyCode === 40 || e.keyCode === 38)) {
            return;
        }
        if (e.target.value.length > 0) {
            this.resultBoxTarget.classList.remove('d-none');
        } else {
            this.resultBoxTarget.classList.add('d-none');
        }
        fetch(this.element.getAttribute('action') + '?q=' + encodeURIComponent(e.target.value), {headers: new Headers({'X-Requested-With': 'XMLHttpRequest'})})
            .then((response) => response.text())
            .then((html) => {
                this.resultBoxTarget.innerHTML = html;
                this.indexValue = 0;
            });

    }

    navigate(e) {
        this.resultBoxTarget.classList.add('pe-none');

        let resetIndex = true;
        if (e.keyCode === 40 && this.indexValue < this.resultItemTargets.length) {
            this.indexValue++;
            resetIndex = false;
        }

        if (e.keyCode === 38 && this.indexValue > 0) {
            this.indexValue--;
            resetIndex = false;
        }

        if (e.keyCode === 13 && this.indexValue > 0) {
            this.currentSelectedResultItem.click();
            e.preventDefault();
            return false;
        }

        if (resetIndex) {
            this.indexValue = 0;
        }


        if (this.indexValue > 0) {
            this.currentSelectedResultItem.scrollIntoView({
                behavior: 'auto',
                block: 'center',
                inline: 'center'
            });
        }
        setTimeout(() => this.resultBoxTarget.classList.remove('pe-none'), 100)
    }

    hover(e) {
        this.indexValue = this.resultItemTargets.indexOf(e.target) + 1;
    }

    indexValueChanged() {
        this.resultItemTargets.forEach((item) => item.classList.remove('active'));

        if (this.indexValue > 0 && this.resultItemTargets.length > 0) {
            this.currentSelectedResultItem.classList.add('active');
        }
    }

    get currentSelectedResultItem() {
        return this.resultItemTargets[this.indexValue - 1];
    }

}
