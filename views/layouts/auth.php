<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= e($title ?? 'Sign In') ?> — Wareflow</title>
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
      'primary':                  '#004ac6',
      'on-primary':               '#ffffff',
      'primary-container':        '#2563eb',
      'error':                    '#dc2626',
      'on-surface':               '#0f172a',
      'on-surface-variant':       '#475569',
      'surface-container-low':    '#f1f5f9',
      'outline-variant':          '#cbd5e1',
    },
  }}
}
</script>
<style>
*, ::before, ::after { font-family: 'Plus Jakarta Sans', sans-serif; }
.material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-variation-settings: 'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; vertical-align: -4px; }
input:focus, select:focus { outline: none; box-shadow: 0 0 0 3px rgba(0,74,198,.2); }

/* Dark mode overrides */
.dark body                    { background: #0c1220 !important; }
.dark .auth-card              { background: #162032 !important; border-color: #243355 !important; }
.dark input, .dark select     { background: #1a2742 !important; border-color: #243355 !important; color: #e2e8f0 !important; }
.dark input::placeholder      { color: #64748b; }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4"
  style="background:linear-gradient(135deg,#eef2ff 0%,#f5f7ff 50%,#e8effe 100%)">

<div class="w-full max-w-[420px]">

  <!-- Logo -->
  <div class="text-center mb-7">
    <div class="inline-flex items-center justify-center w-12 h-12 bg-primary rounded-[14px] mb-3"
      style="box-shadow:0 8px 24px rgba(0,74,198,.25)">
      <span class="material-symbols-outlined text-white" style="font-size:24px">warehouse</span>
    </div>
    <h1 class="text-[22px] font-extrabold text-primary tracking-tight leading-none">Wareflow</h1>
  </div>

  <!-- Flash -->
  <?php $flash = get_flash(); if ($flash): ?>
  <div class="mb-4 px-[14px] py-3 rounded-xl text-[13.5px] flex items-center gap-[10px]
    <?= $flash['type'] === 'error'
      ? 'bg-red-50 text-red-700 border border-red-200'
      : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
    <span class="material-symbols-outlined flex-shrink-0" style="font-size:18px">
      <?= $flash['type'] === 'error' ? 'error' : 'check_circle' ?>
    </span>
    <span><?= $flash['msg'] ?></span>
  </div>
  <?php endif; ?>

  <!-- Card -->
  <div class="auth-card bg-white rounded-[20px] p-9 border border-outline-variant"
    style="box-shadow:0 4px 6px -1px rgba(0,0,0,.05),0 20px 40px -8px rgba(0,0,0,.08)">
    <?= $content ?>
  </div>

  <p class="text-center text-xs text-on-surface-variant mt-5">
    &copy; <?= date('Y') ?> Wareflow. All rights reserved.
  </p>
</div>

<script>
(function () {
  var stored = localStorage.getItem('wf_dark');
  var prefersDark = stored !== null
    ? stored === '1'
    : window.matchMedia('(prefers-color-scheme: dark)').matches;
  if (prefersDark) document.getElementById('html-root').classList.add('dark');
})();
</script>
</body>
</html>
