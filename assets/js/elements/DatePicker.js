import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.min.css'
import '../../css/modules/date.scss'
import {French} from 'flatpickr/dist/l10n/fr.js'

/**
 * @property {flatpickr} flatpickr
 */
export default class DatePicker extends HTMLInputElement {

    connectedCallback() {
        this.flatpickr = flatpickr(this, {
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            locale: French,
            weekNumbers: true,
        });
    }

    disconnectedCallback() {
        this.flatpickr.destroy()
    }

}
global.customElements.define('date-picker', DatePicker, {extends: 'input'})