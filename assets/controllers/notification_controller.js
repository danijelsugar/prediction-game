import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ 'notification', ];

    closeNotification() {
        this.notificationTarget.classList.add('visuallyhidden');
    }

    showNotification(html) {
        this.element.innerHTML = html;

        this.notificationTarget.classList.remove('visuallyhidden');

        setTimeout(() => {
            if (!this.notificationTarget.classList.contains('visuallyhidden')) {
                this.notificationTarget.classList.add('visuallyhidden');
            }
        }, 5000);
    }
}