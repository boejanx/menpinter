import * as bootstrap from 'bootstrap'; // ini akan membuat variabel `bootstrap` global
window.bootstrap = bootstrap;

"use strict";

// Sidebar toggle (custom implementation, replacing Helpers.toggleCollapsed)
const layoutMenuEl = document.getElementById("layout-menu");
const menuToggle = document.querySelectorAll(".layout-menu-toggle");
const logoImg = document.getElementById("brand-logo"); // logo <img>
const brandText = document.querySelector(".app-brand-text"); // teks brand

menuToggle.forEach(item => {
  item.addEventListener("click", event => {
    event.preventDefault();

    const isMobile = window.innerWidth < 1200;

    // === MOBILE MODE (overlay sidebar) ===
    if (isMobile) {
      document.documentElement.classList.toggle('layout-menu-expanded');
      return;
    }

    // === DESKTOP MODE (collapse sidebar) ===
    const isCollapsed = document.body.classList.toggle("layout-menu-collapsed");

    if (logoImg) {
      logoImg.src = isCollapsed
        ? "/assets/img/logo/logo-icon.png"
        : "/assets/img/logo/logo.png";
    }

    if (brandText) {
      brandText.style.display = isCollapsed ? "none" : "inline";
    }

    const icon = item.querySelector("i");
    if (icon) {
      icon.classList.toggle("bx-chevron-left", !isCollapsed);
      icon.classList.toggle("bx-chevron-right", isCollapsed);
    }

    try {
      localStorage.setItem('layout-menu-collapsed', String(isCollapsed));
    } catch (e) {
      console.warn(e);
    }
  });

});


// Tooltip bootstrap init
[].slice
  .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  .map(el => new bootstrap.Tooltip(el));

document.addEventListener('DOMContentLoaded', () => {
  const themeButtons = document.querySelectorAll('[data-bs-theme-value]');
  const themeToggleIcon = document.querySelector('#nav-theme i.theme-icon-active');
  const themeText = document.querySelector('#nav-theme-text');

  const iconMap = {
    light: 'bx bx-sun',
    dark: 'bx bx-moon',
    system: 'bx bx-desktop'
  };

  const applyTheme = (theme) => {
    const html = document.documentElement;

    if (theme === 'system') {
      const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      html.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
    } else {
      html.setAttribute('data-bs-theme', theme);
    }

    // Ganti icon dan teks
    if (themeToggleIcon && iconMap[theme]) {
      themeToggleIcon.className = 'icon-base icon-md theme-icon-active ' + iconMap[theme];
    }

    if (themeText) {
      themeText.textContent = `Theme: ${theme.charAt(0).toUpperCase() + theme.slice(1)}`;
    }

    // Update active state
    themeButtons.forEach(btn => {
      const value = btn.getAttribute('data-bs-theme-value');
      btn.classList.toggle('active', value === theme);
      btn.setAttribute('aria-pressed', value === theme);
    });
  };

  const saveTheme = (theme) => {
    localStorage.setItem('preferred-theme', theme);
  };

  const getSavedTheme = () => {
    return localStorage.getItem('preferred-theme') || 'system';
  };

  // Bind click event
  themeButtons.forEach(button => {
    button.addEventListener('click', () => {
      const selectedTheme = button.getAttribute('data-bs-theme-value');
      applyTheme(selectedTheme);
      saveTheme(selectedTheme);
    });
  });

  document.addEventListener('click', function (e) {
    if (window.innerWidth >= 1200) return;

    const sidebar = document.querySelector('.layout-menu');
    const toggle = e.target.closest('.layout-menu-toggle');
    const backdrop = document.getElementById('layout-menu-backdrop');

    // Klik tombol toggle → abaikan
    if (toggle) return;

    // Klik di dalam sidebar → abaikan
    if (sidebar && sidebar.contains(e.target)) return;

    // Sidebar sedang terbuka?
    if (document.documentElement.classList.contains('layout-menu-expanded')) {
      document.documentElement.classList.remove('layout-menu-expanded');
    }
  });

  document.addEventListener('click', e => {
  if (window.innerWidth >= 1200) return;
  if (!e.target.closest('.menu-link')) return;

  document.documentElement.classList.remove('layout-menu-expanded');
});




  // Initial load
  applyTheme(getSavedTheme());
});
