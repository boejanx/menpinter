/**
 * ===============================
 * CORE IMPORTS
 * ===============================
 */
import './bootstrap';
import './spa.js';
import './main.js';
import './lazyload.js';

/**
 * ===============================
 * jQuery & PLUGINS (GLOBAL)
 * ===============================
 */
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

import select2 from 'select2';
select2($);

import 'bootstrap';
import 'datatables.net-bs5';
import 'datatables.net-responsive';
import 'datatables.net-responsive-bs5';

import 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';

/**
 * ===============================
 * UI LIBRARIES
 * ===============================
 */
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import toastr from 'toastr';

import 'sweetalert2/dist/sweetalert2.min.css';
import 'toastr/build/toastr.min.css';

window.Alpine = Alpine;
window.Swal = Swal;
window.toastr = toastr;

/**
 * ===============================
 * INIT ONCE (ANTI DOUBLE START)
 * ===============================
 */
if (!window.__APP_STARTED__) {
    // Alpine
    Alpine.start();

    // CSRF (safe)
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (csrfToken) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
    }

    // Toastr global config
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3000,
    };

    window.__APP_STARTED__ = true;
}

/**
 * ===============================
 * GLOBAL HELPERS (SPA SAFE)
 * ===============================
 */

// SweetAlert wrapper
window.showAlert = (options = {}) => {
    Swal.fire({
        icon: options.icon || 'info',
        title: options.title || 'Informasi',
        text: options.text || '',
        showConfirmButton: options.showConfirmButton !== false,
        confirmButtonText: options.confirmButtonText || 'OK',
        timer: options.timer || null,
        ...options.customOptions,
    });
};

// Select2 helper (ANTI DOUBLE INIT)
window.initSelect2 = (selector = '.select2') => {
    $(selector).each(function () {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
            });
        }
    });
};

// DataTables destroy-safe helper
window.initDataTable = (selector, options = {}) => {
    if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
    }

    return $(selector).DataTable({
        responsive: true,
        processing: true,
        ...options,
    });
};

/**
 * ===============================
 * SPA MODULE LOADER
 * ===============================
 */
window.pageScripts = {
    dashboard: () => import('./modules/dashboard.js'),
    user: () => import('./modules/user.js'),
    coaching: () => import('./modules/coaching.js'),
    riwayat: () => import('./modules/riwayat.js'),
    bangkom: () => import('./modules/bangkom.js'),
    manajemen_bangkom: () => import('./modules/manajemen-bangkom.js'),
    profile: () => import('./modules/profile.js'),
    verifikasi: () => import('./modules/verifikasi.js'),
    kms: () => import('./modules/kms.js'),
};

export function loadPageScript() {
    const appEl = document.getElementById('app');
    const pageName = appEl?.dataset.page || 'dashboard';

    if (window.__PAGE_LOADED__ === pageName) return;
    window.__PAGE_LOADED__ = pageName;

    if (window.pageScripts[pageName]) {
        window.pageScripts[pageName]().then((module) => {
            if (typeof module.default === 'function') {
                module.default();
            }
        });
    }
}


/**
 * ===============================
 * GLOBAL EVENT HANDLERS
 * ===============================
 */

// Logout (delegated, SPA-safe)
$(document).on('click', '#logout', function (e) {
    e.preventDefault();

    $.post('/api/logout')
        .done(() => {
            toastr.success('Berhasil logout!');
            window.location.href = '/login';
        })
        .fail(() => {
            toastr.error('Gagal logout. Silakan coba lagi.');
        });
});

/**
 * ===============================
 * FIRST LOAD
 * ===============================
 */
document.addEventListener('DOMContentLoaded', loadPageScript);
document.addEventListener('spa:loaded', loadPageScript);


export default loadPageScript;
