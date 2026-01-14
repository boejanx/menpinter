import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

import monthSelectPlugin from 'flatpickr/dist/plugins/monthSelect/index';
import 'flatpickr/dist/plugins/monthSelect/style.css';
// Import plugin
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

export function initDatepickers(selector = '[data-datepicker]', options = {}) {
    const defaultOptions = {
        disableMobile: true,
        allowInput: true,
        maxDate: 'today',
        locale: Indonesian,
    };

    document.querySelectorAll(selector).forEach(el => {
        let customOptions = {};

        switch (el.dataset.datepicker) {
            case 'year':
                customOptions = {
                    dateFormat: 'Y',
                    plugins: [
                        monthSelectPlugin({
                            shorthand: false,
                            dateFormat: "Y",
                            theme: "light"
                        })
                    ]
                };
                break;

            case 'range':
                customOptions = {
                    mode: 'range',
                    dateFormat: 'd/m/Y'
                };
                break;

            default:
                customOptions = {
                    altInput: true,
                    altFormat: "d/m/Y",   // Tampilkan ke user
                    dateFormat: "Y-m-d"
                };
        }

        flatpickr(el, { ...defaultOptions, ...customOptions, ...options });
    });
}
