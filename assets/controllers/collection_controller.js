import { Controller } from 'stimulus';
import 'symfony-collection/jquery.collection';

export default class extends Controller {

    connect() {
        const collectionOptions = {
            up: '<a href="#" class="btn btn-light"><span class="fa fa-arrow-up"></span></a>',
            down: '<a href="#" class="btn btn-light"><span class="fa fa-arrow-down"></span></a>',
            remove: '<a href="#" class="btn btn-light text-danger"><span class="fa fa-times"></span></a>',
            add_at_the_end: true,
            fade_in: false,
            fade_out: false,
            after_add: function (collection, element) {
                collection.trigger('collection_add', element);
                return true;
            },
            before_remove: function (collection, element) {
                collection.trigger('collection_remove', element);
                return true;
            }
        };

        const $element = $(this.element);

        let options = collectionOptions;

        const max = $element.attr('data-max');
        if (max > 0) {
            options = Object.assign({}, options, {max: max})
        }
        const min = $element.attr('data-min');
        if (min > 0) {
            options = Object.assign({}, options, {min: min})
        }
        let dragDrop = $element.attr('data-drag-drop');
        if (dragDrop !== undefined) {
            dragDrop = !(dragDrop === 'false' || dragDrop === '0');
            options = Object.assign({}, options, {drag_drop: dragDrop})
        }
        const addLabel = $element.attr('data-add-label');
        options = Object.assign({}, options, {add: '<a href="#" class="btn btn-outline-secondary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> ' + addLabel + '</a>'})

        $element.collection(options);
    }

}
