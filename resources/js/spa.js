import NProgress from "nprogress";
import "nprogress/nprogress.css";

const app = document.getElementById("app");

async function showLoader() {
  NProgress.start();
  await fadeOut(app, 400); // fade out konten dulu
  app.style.pointerEvents = "none";
}

async function hideLoader() {
  NProgress.done();
  await fadeIn(app, 400); // fade in konten setelah loading selesai
  app.style.pointerEvents = "";
}


function fadeOut(element, duration = 400) {
  return new Promise((resolve) => {
    element.style.transition = `opacity ${duration}ms ease-in-out`;
    element.style.opacity = 0;
    setTimeout(resolve, duration);
  });
}

function fadeIn(element, duration = 400) {
  return new Promise((resolve) => {
    element.style.transition = `opacity ${duration}ms ease-in-out`;
    element.style.opacity = 1;
    setTimeout(resolve, duration);
  });
}

function setActiveMenu(url) {
  const links = document.querySelectorAll(".menu-link");
  links.forEach((link) => {
    const li = link.closest(".menu-item");
    if (!li) return;

    const linkUrl = new URL(link.href, location.origin).pathname;
    const targetUrl = new URL(url, location.origin).pathname;

    li.classList.toggle("active", linkUrl === targetUrl);
  });
}

function reinitPageScripts() {
  const pageName = app.dataset.page;
  if (!pageName) return;

  // Panggil script sesuai page
  import("./app.js").then(({ loadPageScript }) => {
    if (typeof loadPageScript === "function") {
      requestIdleCallback(() => loadPageScript());
    }
  });

  import("./lazyload.js").then(({ initLazyImages }) => {
    requestIdleCallback(() => initLazyImages());
  });
}



const pageCache = new Map();

async function ajaxNavigate(url) {
  const cleanUrl = new URL(url, location.origin).href;


  await showLoader();

  if (pageCache.has(cleanUrl)) {
    updatePageFromHTML(pageCache.get(cleanUrl), cleanUrl);
    await hideLoader();
    return;
  }

  fetch(cleanUrl, {
    headers: { "X-Requested-With": "XMLHttpRequest" },
  })
    .then((response) => {
      if (!response.ok) throw new Error("HTTP error " + response.status);
      return response.text();
    })
    .then(async (html) => {
      pageCache.set(cleanUrl, html);
      updatePageFromHTML(html, cleanUrl);
      await hideLoader();
    })
    .catch(async (error) => {
      console.error("AJAX Error:", error);

      const status = error.status || error.response?.status;

      switch (status) {
        case 401:
          // Unauthorized - Redirect ke login
          window.location.href = '/login.html';
          return;

        case 403:
          // Forbidden - Tidak memiliki akses
          app.innerHTML = `
        <div class="alert alert-warning" role="alert">
          <strong>Akses Ditolak:</strong> Anda tidak memiliki izin untuk mengakses halaman ini.
        </div>`;
          break;

        case 404:
          // Not Found
          app.innerHTML = `
        <div class="alert alert-warning" role="alert">
          <strong>Halaman Tidak Ditemukan:</strong> Konten yang Anda cari tidak tersedia.
        </div>`;
          break;

        case 500:
          // Server Error
          app.innerHTML = `
        <div class="alert alert-danger" role="alert">
          <strong>Kesalahan Server:</strong> Terjadi masalah pada server. Silakan coba lagi nanti.
        </div>`;
          break;

        default:
          // Error lainnya
          app.innerHTML = `
        <div class="alert alert-danger" role="alert">
          <strong>Gagal memuat konten:</strong> ${error.message || 'Terjadi kesalahan yang tidak diketahui'}
        </div>`;
      }

      await hideLoader();
    });
}


function updatePageFromHTML(html, url) {
  const parser = new DOMParser();
  const doc = parser.parseFromString(html, "text/html");
  const newApp = doc.getElementById("app");
  if (!newApp) throw new Error("#app tidak ditemukan");

  const pageName = newApp.dataset.page || "";

  app.setAttribute("data-page", pageName);
  app.innerHTML = newApp.innerHTML;

  const newFooter = doc.querySelector(".footer");
  if (newFooter) {
    const currentFooter = document.querySelector(".footer");
    if (currentFooter) currentFooter.replaceWith(newFooter);
  }

  document.title = doc.title;
  window.history.pushState({}, "", url);

  setActiveMenu(url);
  reinitPageScripts();
  hideLoader();
}

document.addEventListener("DOMContentLoaded", () => {
  setActiveMenu(window.location.href);

  document.body.addEventListener("click", function (e) {
    const link = e.target.closest(".menu-link");
    if (link) {
      e.preventDefault();
      const url = link.getAttribute("href");
      if (url && url !== window.location.href) {
        ajaxNavigate(url);
      }
    }
  });

  window.addEventListener("popstate", () => {
    ajaxNavigate(location.href);
  });

  app.classList.add("loaded");
});

export { ajaxNavigate };
