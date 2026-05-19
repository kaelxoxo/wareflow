<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($title ?? 'Dashboard') ?> — Wareflow</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
tailwind.config = {
  darkMode: 'class',
  theme: { extend: {
    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
    colors: {
      'primary':                    '#004ac6',
      'on-primary':                 '#ffffff',
      'primary-container':          '#2563eb',
      'on-primary-container':       '#eeefff',
      'secondary':                  '#475569',
      'on-secondary':               '#ffffff',
      'secondary-container':        '#e2e8f0',
      'on-secondary-container':     '#334155',
      'tertiary':                   '#3e3fcc',
      'tertiary-container':         '#585be6',
      'on-tertiary-container':      '#f1eeff',
      'error':                      '#dc2626',
      'error-container':            '#ffdad6',
      'on-error-container':         '#93000a',
      'background':                 '#f8fafc',
      'on-background':              '#0f172a',
      'surface':                    '#f8fafc',
      'on-surface':                 '#0f172a',
      'surface-variant':            '#e2e8f0',
      'on-surface-variant':         '#475569',
      'surface-container-lowest':   '#ffffff',
      'surface-container-low':      '#f1f5f9',
      'surface-container':          '#e8edf5',
      'surface-container-high':     '#dde3ed',
      'surface-container-highest':  '#d0d9e8',
      'outline':                    '#94a3b8',
      'outline-variant':            '#cbd5e1',
      'inverse-surface':            '#1e293b',
      'inverse-on-surface':         '#f1f5f9',
      'inverse-primary':            '#93c5fd',
    },
    spacing: {
      'xs': '4px', 'sm': '12px', 'md': '16px',
      'lg': '24px', 'xl': '32px', 'gutter': '24px',
    },
  }}
}
</script>
<style>
*, ::before, ::after { font-family: 'Plus Jakarta Sans', sans-serif; }
.material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; vertical-align: -4px; }

/* Sidebar nav */
.nav-link {
  display: flex; align-items: center; gap: 10px; padding: 9px 14px;
  border-radius: 10px; font-size: 13.5px; font-weight: 500;
  transition: all .15s ease; color: #64748b; cursor: pointer;
}
.nav-link:hover { background: #e8edf5; color: #0f172a; }
.nav-link.active { background: #eff4ff; color: #004ac6; font-weight: 600; }
.nav-link:focus-visible {
  outline: 2px solid rgba(0,74,198,.45);
  outline-offset: 1px;
}
.nav-link .nav-icon { font-size: 18px; flex-shrink: 0; }
.dark .nav-link { color: #94a3b8; }
.dark .nav-link:hover { background: rgba(255,255,255,.07); color: #e2e8f0; }
.dark .nav-link.active { background: rgba(59,130,246,.15); color: #60a5fa; }

/* Dark mode overrides */
.dark body { background: #0c1220; color: #e2e8f0; }
.dark .bg-surface-container-lowest { background: #162032 !important; }
.dark .bg-surface-container-low    { background: #1a2742 !important; }
.dark .bg-surface-container        { background: #1e2d4e !important; }
.dark .bg-surface-container-high   { background: #243355 !important; }
.dark .bg-surface, .dark .bg-background { background: #0c1220 !important; }
.dark .border-outline-variant { border-color: #243355 !important; }
.dark .text-on-surface { color: #e2e8f0 !important; }
.dark .text-on-surface-variant { color: #94a3b8 !important; }
.dark .card { background: #162032; border-color: #243355; }
.dark .modal { background: #162032; border: 1px solid #243355; }

/* Flash messages */
.dark .flash-error   { background: rgba(220,38,38,.12)  !important; color: #fca5a5 !important; border-color: rgba(220,38,38,.25) !important; }
.dark .flash-success { background: rgba(16,185,129,.10) !important; color: #6ee7b7 !important; border-color: rgba(16,185,129,.22) !important; }
.dark .flash-error   .flash-icon { color: #f87171 !important; }
.dark .flash-success .flash-icon { color: #34d399 !important; }

/* Inputs */
input, select, textarea {
  transition: box-shadow .15s, border-color .15s;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
input:focus, select:focus, textarea:focus {
  outline: none; box-shadow: 0 0 0 3px rgba(0,74,198,.18);
}
.dark input, .dark select, .dark textarea {
  background: #1a2742; border-color: #1e2d4e; color: #e2e8f0;
}

/* Buttons */
.btn-primary {
  background: #004ac6; color: #fff; padding: 8px 20px; border-radius: 10px;
  font-size: 13.5px; font-weight: 600; transition: all .15s; cursor: pointer;
}
.btn-primary:hover { background: #0053db; box-shadow: 0 4px 14px rgba(0,74,198,.28); }
.btn-primary:focus-visible {
  outline: 2px solid rgba(0,74,198,.5);
  outline-offset: 2px;
}
.btn-secondary {
  background: #f1f5f9; color: #334155; padding: 8px 20px; border-radius: 10px;
  font-size: 13.5px; font-weight: 600; transition: all .15s; border: 1px solid #e2e8f0; cursor: pointer;
}
.btn-secondary:hover { background: #e2e8f0; }
.btn-secondary:focus-visible {
  outline: 2px solid rgba(0,74,198,.4);
  outline-offset: 2px;
}
.dark .btn-secondary { background: #1a2742; color: #93c5fd; border-color: #1e2d4e; }

/* Cards */
.card {
  background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
  padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,.05);
  transition: box-shadow .15s;
}

/* Table rows */
.table-row:hover { background: #f8fafc; }
.dark .table-row:hover { background: rgba(255,255,255,.03); }

/* Badge */
.badge {
  display: inline-flex; align-items: center; padding: 2px 9px;
  border-radius: 6px; font-size: 11.5px; font-weight: 600;
}

/* Modal */
[x-cloak] { display: none; }
.modal-backdrop {
  position: fixed; inset: 0; background: rgba(0,0,0,.45);
  backdrop-filter: blur(2px); z-index: 50; display: flex;
  align-items: center; justify-content: center; padding: 16px;
}
.modal {
  background: #fff; border-radius: 18px; padding: 32px;
  max-width: 520px; width: 100%;
  box-shadow: 0 24px 64px rgba(0,0,0,.15);
  animation: slideIn .2s ease;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(-10px) scale(.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-in { animation: slideIn .2s ease; }

/* Sidebar overlay (mobile) */
#sidebar-overlay {
  display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4);
  backdrop-filter: blur(2px); z-index: 30;
}
#sidebar-overlay.open { display: block; }

/* Sidebar mobile */
@media (max-width: 1023px) {
  #sidebar { transform: translateX(-100%); transition: transform .25s ease; z-index: 40; }
  #sidebar.open { transform: translateX(0); }
  #main-content { margin-left: 0 !important; }
}

/* Dark hover overrides for icon-button affordances */
.dark .hover\:bg-red-50:hover   { background: rgba(220,38,38,.12)  !important; }
.dark .hover\:bg-amber-50:hover { background: rgba(217,119,6,.10)  !important; }
.dark .hover\:bg-blue-50:hover  { background: rgba(59,130,246,.12) !important; }

/* Dark surface-container hover overrides — without these Tailwind resolves to light defaults */
.dark .hover\:bg-surface-container-lowest:hover { background: #162032 !important; }
.dark .hover\:bg-surface-container-low:hover    { background: #1a2742 !important; }
.dark .hover\:bg-surface-container:hover        { background: #1e2d4e !important; }
.dark .hover\:bg-surface-container-high:hover   { background: #243355 !important; }

/* Scrollbar */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
.dark ::-webkit-scrollbar-thumb { background: #334155; }

@media (prefers-reduced-motion: reduce) {
  *, ::before, ::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
  .modal, .animate-in { animation: none !important; }
  #sidebar { transition: none !important; }
}
</style>
</head>
<body class="bg-background text-on-surface min-h-screen">

<!-- Mobile overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()" aria-hidden="true" role="presentation"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed left-0 top-0 h-full w-[260px] bg-surface-container-lowest border-r border-outline-variant flex flex-col" style="z-index:40" aria-label="Application sidebar">

  <!-- Logo — links to dashboard -->
  <div class="px-lg h-16 flex items-center border-b border-outline-variant">
    <a href="<?= url('/dashboard') ?>"
      class="flex items-center gap-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-lg"
      aria-label="Wareflow — go to dashboard">
      <div class="w-8 h-8 bg-primary rounded-[10px] flex items-center justify-center flex-shrink-0" aria-hidden="true">
        <span class="material-symbols-outlined text-white" style="font-size:17px">warehouse</span>
      </div>
      <div class="min-w-0">
        <p class="text-[15px] font-bold text-primary leading-none tracking-tight">Wareflow</p>
        <p class="text-[11px] text-on-surface-variant mt-0.5 truncate"><?= e(Auth::user()['tenant_name'] ?? '') ?></p>
      </div>
    </a>
  </div>

  <!-- Nav -->
  <nav class="flex-1 px-sm py-sm space-y-0.5 overflow-y-auto" aria-label="Main navigation">
    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-widest px-3 pt-2 pb-1.5">Main</p>
    <a href="<?= url('/dashboard') ?>" class="nav-link <?= is_active('/dashboard') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">dashboard</span>
      <span>Dashboard</span>
    </a>
    <a href="<?= url('/inventory') ?>" class="nav-link <?= is_active('/inventory') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">inventory_2</span>
      <span>Inventory</span>
    </a>
    <a href="<?= url('/warehouses') ?>" class="nav-link <?= is_active('/warehouses') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">warehouse</span>
      <span>Warehouses</span>
    </a>
    <a href="<?= url('/stock') ?>" class="nav-link <?= is_active('/stock') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">swap_horiz</span>
      <span>Stock Movements</span>
    </a>

    <p class="text-[10.5px] font-semibold text-on-surface-variant uppercase tracking-widest px-3 pt-4 pb-1.5">Configure</p>
    <?php if (Auth::can('manage_custom_fields')): ?>
    <a href="<?= url('/custom-fields') ?>" class="nav-link <?= is_active('/custom-fields') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">tune</span>
      <span>Custom Fields</span>
    </a>
    <?php endif; ?>
    <?php if (Auth::can('manage_users')): ?>
    <a href="<?= url('/users') ?>" class="nav-link <?= is_active('/users') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">group</span>
      <span>Users &amp; Roles</span>
    </a>
    <?php endif; ?>
    <?php if (Auth::can('manage_settings')): ?>
    <a href="<?= url('/settings') ?>" class="nav-link <?= is_active('/settings') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">settings</span>
      <span>Settings</span>
    </a>
    <?php endif; ?>
    <?php if (Auth::role() === 'owner'): ?>
    <a href="<?= url('/billing') ?>" class="nav-link <?= is_active('/billing') ?>">
      <span class="material-symbols-outlined nav-icon" aria-hidden="true">credit_card</span>
      <span>Billing</span>
    </a>
    <?php endif; ?>
  </nav>

  <!-- User footer -->
  <div class="px-sm pb-sm border-t border-outline-variant pt-sm">
    <div class="flex items-center gap-sm px-sm py-[10px] rounded-xl hover:bg-surface-container-low transition-colors">
      <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
        <span class="text-[13px] font-bold text-white"><?= strtoupper(substr(Auth::user()['name'] ?? 'U', 0, 1)) ?></span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-[13px] font-semibold truncate text-on-surface"><?= e(Auth::user()['name'] ?? '') ?></p>
        <p class="text-[11px] text-on-surface-variant capitalize truncate"><?= e(Auth::role()) ?></p>
      </div>
      <!-- Logout: always visible, muted at rest, red on hover -->
      <a href="<?= url('/logout') ?>"
        class="flex-shrink-0 p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40"
        aria-label="Sign out"
        title="Sign out">
        <span class="material-symbols-outlined" style="font-size:16px" aria-hidden="true">logout</span>
      </a>
    </div>
  </div>
</aside>

<!-- Main content -->
<div id="main-content" class="ml-0 lg:ml-[260px] min-h-screen flex flex-col">

  <!-- Top bar -->
  <header class="sticky top-0 z-20 bg-surface-container-lowest border-b border-outline-variant flex items-center gap-md px-lg h-16">

    <!-- Hamburger (mobile) -->
    <button id="sidebar-toggle" onclick="openSidebar()" aria-label="Open navigation menu" aria-expanded="false" aria-controls="sidebar"
      class="lg:hidden p-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors -ml-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
      <span class="material-symbols-outlined" style="font-size:22px" aria-hidden="true">menu</span>
    </button>

    <!-- Search — proper form with visible submit affordance -->
    <form id="search-form" role="search" class="relative flex-1 max-w-sm" onsubmit="return handleSearch(event)">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none" style="font-size:17px" aria-hidden="true">search</span>
      <input type="search" name="search" id="global-search"
        placeholder="Search inventory, SKUs…"
        autocomplete="off" maxlength="120"
        class="w-full bg-surface-container-low border border-outline-variant rounded-xl pl-9 pr-9 py-[7px] text-sm focus:border-primary"/>
      <button type="submit"
        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-0.5 rounded text-on-surface-variant hover:text-on-surface transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary/40"
        aria-label="Submit search">
        <span class="material-symbols-outlined" style="font-size:15px" aria-hidden="true">keyboard_return</span>
      </button>
    </form>

    <div class="flex items-center gap-xs ml-auto">
      <!-- Dark mode toggle -->
      <button onclick="toggleDark()" aria-label="Toggle dark mode" title="Toggle dark mode"
        class="p-2 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/40">
        <span class="material-symbols-outlined" id="theme-icon" style="font-size:20px" aria-hidden="true">dark_mode</span>
      </button>

      <?php if (Auth::can('manage_inventory')): ?>
      <div class="w-px h-5 bg-outline-variant" aria-hidden="true"></div>
      <!-- Add item -->
      <a href="<?= url('/inventory/create') ?>" class="btn-primary flex items-center gap-xs text-[13px] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-primary/50">
        <span class="material-symbols-outlined" style="font-size:15px" aria-hidden="true">add</span>
        <span class="hidden sm:inline">Add Item</span>
      </a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Past-due subscription banner -->
  <?php if ((Auth::user()['tenant_subscription_status'] ?? '') === 'past_due'): ?>
  <div class="px-xl pt-lg">
    <div class="flex items-center gap-sm px-md py-3 bg-amber-50 border border-amber-200 rounded-xl dark:bg-amber-900/10 dark:border-amber-700/30">
      <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 flex-shrink-0" style="font-size:17px">warning</span>
      <p class="text-sm text-amber-800 dark:text-amber-300 flex-1">
        Your last payment failed. Access may be restricted soon.
        <?php if (Auth::role() === 'owner'): ?>
          <a href="<?= url('/billing') ?>" class="font-semibold underline">Update payment method &rarr;</a>
        <?php else: ?>
          Please contact your workspace owner.
        <?php endif; ?>
      </p>
    </div>
  </div>
  <?php endif; ?>

  <!-- Flash message -->
  <?php $flash = get_flash(); if ($flash): ?>
  <div id="flash-msg" class="px-xl pt-lg animate-in" role="alert" aria-live="assertive">
    <?php
    $isError = $flash['type'] === 'error';
    $cls  = $isError ? 'flash-error bg-red-50 text-red-800 border border-red-200' : 'flash-success bg-emerald-50 text-emerald-800 border border-emerald-200';
    $icon = $isError ? 'error' : 'check_circle';
    $icls = $isError ? 'flash-icon text-red-500' : 'flash-icon text-emerald-500';
    ?>
    <div class="flex items-start gap-sm p-md rounded-xl <?= $cls ?>">
      <span class="material-symbols-outlined <?= $icls ?>" style="font-size:19px;margin-top:1px" aria-hidden="true"><?= $icon ?></span>
      <p class="text-sm flex-1 leading-relaxed"><?= $flash['msg'] ?></p>
      <button onclick="this.closest('#flash-msg').remove()" class="opacity-50 hover:opacity-100 transition-opacity focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-current rounded" aria-label="Dismiss message">
        <span class="material-symbols-outlined" style="font-size:17px" aria-hidden="true">close</span>
      </button>
    </div>
  </div>
  <?php endif; ?>

  <!-- Page content -->
  <main class="flex-1 p-lg lg:p-xl">
    <?= $content ?>
  </main>

</div>

<script src="<?= asset('js/app.js') ?>"></script>
<script>
// Dark mode — respects OS preference on first load, then localStorage
function toggleDark() {
  const html = document.getElementById('html-root');
  html.classList.toggle('dark');
  const isDark = html.classList.contains('dark');
  localStorage.setItem('wf_dark', isDark ? '1' : '0');
  document.getElementById('theme-icon').textContent = isDark ? 'light_mode' : 'dark_mode';
}
(function () {
  const stored = localStorage.getItem('wf_dark');
  const prefersDark = stored !== null
    ? stored === '1'
    : window.matchMedia('(prefers-color-scheme: dark)').matches;
  if (prefersDark) {
    document.getElementById('html-root').classList.add('dark');
    document.getElementById('theme-icon').textContent = 'light_mode';
  }
})();

// Sidebar mobile — manages aria-expanded on the toggle button
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebar-overlay').classList.add('open');
  document.getElementById('sidebar-toggle').setAttribute('aria-expanded', 'true');
  document.body.style.overflow = 'hidden';
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('open');
  const toggle = document.getElementById('sidebar-toggle');
  if (toggle) toggle.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
}

// Global search — form submission, navigates to inventory with query
function handleSearch(e) {
  e.preventDefault();
  const q = document.getElementById('global-search').value.trim();
  if (q) window.location = '<?= url('/inventory') ?>?search=' + encodeURIComponent(q);
  return false;
}

// Flash auto-dismiss
setTimeout(function () {
  const f = document.getElementById('flash-msg');
  if (f) { f.style.transition = 'opacity .4s'; f.style.opacity = '0'; setTimeout(function () { f.remove(); }, 400); }
}, 5000);

// Modal helpers + Escape closes modals and sidebar
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-backdrop:not(.hidden)').forEach(function (m) { m.classList.add('hidden'); });
    if (document.getElementById('sidebar').classList.contains('open')) closeSidebar();
  }
});
</script>
</body>
</html>
