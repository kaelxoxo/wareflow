// Wareflow — client-side utilities

// Dark mode — apply before paint to avoid flash
(function () {
  if (localStorage.getItem('wf_dark') === '1') {
    document.documentElement.classList.add('dark');
  }
})();

document.addEventListener('DOMContentLoaded', function () {

  // Auto-close flash messages
  const flash = document.getElementById('flash-msg');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .4s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 400);
    }, 5000);
  }

  // Confirm-on-submit for elements with data-confirm
  document.querySelectorAll('[data-confirm]').forEach(function (el) {
    el.addEventListener('submit', function (e) {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // Active nav highlight — exact match for dashboard, prefix match for others
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-link').forEach(function (link) {
    const href = link.getAttribute('href');
    if (!href) return;
    try {
      const linkPath = new URL(href, window.location.origin).pathname;
      const isExact  = linkPath === currentPath;
      const isPrefix = linkPath !== '/' && currentPath.startsWith(linkPath);
      if (isExact || isPrefix) link.classList.add('active');
    } catch (_) {}
  });

  // Global search debounce (future: inline suggestions)
  const searchInput = document.getElementById('global-search');
  if (searchInput) {
    let timer;
    searchInput.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(() => { /* future: autocomplete */ }, 300);
    });
  }

  // Close sidebar on nav link click (mobile)
  document.querySelectorAll('#sidebar .nav-link').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth < 1024) {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('open');
        document.body.style.overflow = '';
      }
    });
  });

});
