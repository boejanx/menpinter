import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';

/**
 * Inisialisasi Quill Editor reusable
 *
 * @param {string} editorSelector - CSS selector untuk kontainer editor (div)
 * @param {string} inputSelector - CSS selector untuk input hidden (untuk simpan isi)
 * @param {Object} customOptions - Opsi tambahan (optional)
 * @returns {Quill|null} - Instance Quill jika berhasil
 */
export default function initQuillEditor(editorSelector, inputSelector, customOptions = {}) {
    const editorEl = document.querySelector(editorSelector);
    const inputEl = document.querySelector(inputSelector);

    if (!editorEl || !inputEl) {
        console.warn('Quill editor or input element not found');
        return null;
    }

    const options = {
        theme: 'snow',
        placeholder: 'Tulis konten di sini...',
        modules: {
            toolbar: true,
        },
    };

    const quill = new Quill(editorEl, options);

    // Sync content ke hidden input
    quill.on('text-change', function () {
        inputEl.value = quill.root.innerHTML;
    });

    // Load isi awal jika ada
    if (inputEl.value) {
        quill.root.innerHTML = inputEl.value;
    }

    return quill;
}
