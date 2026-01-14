import Swal from "sweetalert2";
import { initLazyImages } from "../lazyload.js";

// Loading states management for buttons
const loadingStates = {};

/**
 * Handles the load more functionality.
 * 
 * @param {Event} e The event object.
 */
const handleLoadMore = async (e) => {
    const button = e.target.closest("#load-more");
    if (!button || loadingStates[button.id]) return;

    // Set loading state
    loadingStates[button.id] = true;
    button.disabled = true;
    button.innerText = "Loading...";

    try {
        const nextPage = button.dataset.nextPage;
        const url = `/bangkom?page=${nextPage}`;

        // Fetch next page content
        const response = await fetch(url, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        if (!response.ok) throw new Error("Failed to load data");

        // Parse HTML content
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");

        // Append new items to the event list
        const newItems = doc.querySelectorAll("#event-list .col");
        const eventList = document.querySelector("#event-list");
        newItems.forEach((col) => eventList.appendChild(col));

        // Re-initialize lazy images
        initLazyImages();

        // Update load more button
        const newLoadMore = doc.querySelector("#load-more");
        if (newLoadMore) {
            button.dataset.nextPage = newLoadMore.dataset.nextPage;
            button.disabled = false;
            button.innerText = "Load More";
        } else {
            button.remove();
        }
    } catch (error) {
        console.error(error);
        button.innerText = "Failed, try again";
    } finally {
        loadingStates[button.id] = false;
    }
};

/**
 * Handles the registration for an event.
 * 
 * @param {Event} e The event object.
 */
const handleDaftarBangkom = async (e) => {
    e.preventDefault();

    // Confirmation dialog
    Swal.fire({
        title: "Ikuti Kegiatan?",
        text: "apakah anda ingin mengikuti acara ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Daftar",
        cancelButtonText: "Batal",
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        const form = document.getElementById("formDaftarBangkom");
        const url = form.getAttribute("action");
        const formData = new FormData(form);

        try {
            // Register user for the event
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": form.querySelector('[name="_token"]').value,
                },
                body: formData,
            });

            if (!response.ok) throw new Error("Failed to register");

            // Success notification and reload
            Swal.fire({
                title: "Success!",
                text: "anda telah terdaftar pada acara ini.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
            }).then(() => {
                window.location.reload();
            });
        } catch (error) {
            console.error(error);
            Swal.fire("Failed!", "An error occurred during registration.", "error");
        }
    });
};

const isiKehadiran = async (e) => {
    e.preventDefault();

    const button = e.target.closest("#isi-kehadiran");
    if (!button) return;

    const url = button.dataset.url;
    if (!url) {
        console.error("Data URL tidak ditemukan pada tombol presensi");
        return;
    }

    Swal.fire({
        title: "Isi Kehadiran?",
        text: "Anda akan mengisi presensi untuk acara ini.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, Isi Presensi",
        cancelButtonText: "Batal",
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        button.disabled = true;
        const originalText = button.innerText;
        button.innerText = "Memproses...";

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
            });

            const result = await response.json();

            if (response.ok && result.status === "success") {
                // Ubah tampilan tombol jadi "Sudah Hadir"
                button.innerHTML = "Anda Sudah Mengisi Presensi";
                button.classList.remove("btn-info");
                button.classList.add("btn-success");
                button.disabled = true;

                Swal.fire({
                    title: "Presensi Berhasil!",
                    text: result.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false,
                });

            } else if (result.status === "info") {
                Swal.fire({
                    title: "Sudah Presensi",
                    text: result.message,
                    icon: "info",
                });

                // Ubah tombol juga
                button.innerHTML = "Anda Sudah Mengisi Presensi";
                button.classList.remove("btn-info");
                button.classList.add("btn-success");
                button.disabled = true;
            } else {
                throw new Error(result.message || "Presensi gagal dikirim.");
            }
        } catch (error) {
            console.error(error);
            Swal.fire("Gagal!", error.message, "error");
            button.disabled = false;
            button.innerText = originalText;
        }
    });
};






/**
 * Initializes event listeners for the Bangkom page.
 */
export default function initBangkomPage() {
    document.body.addEventListener("click", (e) => {
        const button = e.target.closest("#load-more");
        if (button) {
            handleLoadMore(e);
            return;
        }

        const daftarButton = e.target.closest("#daftar-bangkom");
        if (daftarButton) {
            handleDaftarBangkom(e);
        }

        const presensiButton = e.target.closest("#isi-kehadiran");
        if (presensiButton) {
            isiKehadiran(e);
            return;
        }
    });
}