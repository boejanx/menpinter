import './bootstrap';
import './spa.js';
import './main.js';
import './lazyload.js';


// ✅ jQuery + Select2 setup (HARUS di sini)
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

import 'bootstrap';
import 'datatables.net-bs5';
import 'datatables.net-responsive'; 
import 'datatables.net-responsive-bs5';

import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css';
select2();

// ✅ Alpine & SweetAlert
import Alpine from 'alpinejs';
window.Alpine = Alpine;

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

window.Swal = Swal;

if (!window.AlpineStarted) {
    Alpine.start();
    window.AlpineStarted = true;
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// ✅ SPA Module Loader
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
    let pageName = appEl?.dataset.page;

    if (!pageName) {
        pageName = 'dashboard';
    }

    if (pageScripts[pageName]) {
        pageScripts[pageName]().then((module) => {
            if (module.default && typeof module.default === 'function') {
                module.default();
            }
        });
    }
}

function setupLogoutHandler() {
    $(document).on('click', '#logout', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'api/logout',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function () {
                toastr.success('Berhasil logout!');
                window.location.href = '/login';
            },
            error: function () {
                toastr.error('Gagal logout. Silakan coba lagi.');
            },
        });
    });
}

window.showAlert = function (options = {}) {
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

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: '3000',
};

document.addEventListener('DOMContentLoaded', () => {
    loadPageScript();
    setupLogoutHandler();
});

export default loadPageScript;
