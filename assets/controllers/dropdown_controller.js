import { Controller } from '@hotwired/stimulus';
import { useHover } from 'stimulus-use'

export default class extends Controller {
    static targets = [ "dropdown" ]

    connect() {
        useHover(this, { element: this.dropdownTarget });
    }

    show() {
        this.dropdownTarget.classList.add('d-block');
      }
    
    hide() {
        this.dropdownTarget.classList.remove('d-block');
    }
}
