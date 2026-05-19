<?php
// Digital Serenity effects adapted for Wareflow — no React, no TS, no build step
// Stack: PHP + Tailwind CDN + vanilla JS
?>

<!-- ─── Embedded animation styles ─── -->
<style>
/* Word entrance */
@keyframes word-appear {
  0%   { opacity:0; transform:translateY(28px) scale(.82); filter:blur(10px); }
  50%  { opacity:.85; transform:translateY(8px) scale(.97); filter:blur(2px); }
  100% { opacity:1; transform:translateY(0) scale(1); filter:blur(0); }
}
/* SVG grid lines drawing themselves */
@keyframes grid-draw {
  0%   { stroke-dashoffset:1000; opacity:0; }
  50%  { opacity:.5; }
  100% { stroke-dashoffset:0; opacity:.2; }
}
/* Pulsing intersection dots */
@keyframes pulse-glow {
  0%,100% { opacity:.12; transform:scale(1); }
  50%      { opacity:.45; transform:scale(1.15); }
}
/* Floating particle float */
@keyframes float-particle {
  0%,100% { transform:translateY(0) translateX(0); opacity:.25; }
  25%      { transform:translateY(-12px) translateX(6px); opacity:.7; }
  50%      { transform:translateY(-6px) translateX(-4px); opacity:.45; }
  75%      { transform:translateY(-18px) translateX(8px); opacity:.85; }
}
/* Underline grow */
@keyframes underline-grow {
  to { width:100%; }
}
/* KPI card slide-up fade (dashboard) */
@keyframes slideUpFade {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
/* Section reveal */
@keyframes fadeReveal {
  from { opacity:0; transform:translateY(16px); }
  to   { opacity:1; transform:translateY(0); }
}
/* Ripple */
@keyframes ripple-out {
  0%   { width:4px; height:4px; opacity:.7; }
  100% { width:80px; height:80px; opacity:0; }
}

/* Word tokens */
.word-animate {
  display:inline-block;
  opacity:0;
  margin:0 .08em;
  transition:color .25s, transform .25s;
  cursor:default;
}
.word-animate:hover { color:#93c5fd; transform:translateY(-2px); }

/* Animated grid lines */
.grid-line {
  stroke:#3b82f6;
  stroke-width:.6;
  opacity:0;
  stroke-dasharray:6 6;
  stroke-dashoffset:1000;
  animation:grid-draw 2.2s ease-out forwards;
}

/* Pulsing dots */
.detail-dot {
  fill:#60a5fa;
  opacity:0;
  animation:pulse-glow 3.5s ease-in-out infinite;
}

/* Corner decorative brackets */
.corner-bracket {
  position:absolute;
  width:44px;
  height:44px;
  border:1.5px solid rgba(147,197,253,.22);
  opacity:0;
  animation:word-appear 1s ease-out forwards;
}

/* Floating particles */
.float-dot {
  position:absolute;
  width:3px;
  height:3px;
  background:#60a5fa;
  border-radius:50%;
  opacity:0;
  animation:float-particle 4.5s ease-in-out infinite;
  animation-play-state:paused;
}

/* Mouse-tracking radial gradient */
#mouse-gradient {
  position:fixed;
  pointer-events:none;
  border-radius:9999px;
  background:radial-gradient(circle, rgba(0,74,198,.1), rgba(59,130,246,.06), transparent 70%);
  transform:translate(-50%,-50%);
  transition:left 70ms linear, top 70ms linear, opacity .3s ease;
  will-change:left,top;
  z-index:5;
  opacity:0;
}

/* Click ripple */
.click-ripple {
  position:fixed;
  border-radius:50%;
  background:rgba(147,197,253,.5);
  transform:translate(-50%,-50%);
  pointer-events:none;
  animation:ripple-out .8s ease-out forwards;
  z-index:9999;
  width:4px;
  height:4px;
}

/* Hero underline accent */
.underline-accent {
  position:relative;
  display:inline-block;
}
.underline-accent::after {
  content:'';
  position:absolute;
  bottom:-5px;
  left:0;
  width:0;
  height:2px;
  background:linear-gradient(90deg,transparent,#3b82f6,transparent);
  animation:underline-grow 2s ease-out forwards;
  animation-delay:1.0s;
}

/* Feature card reveal */
.reveal-card {
  opacity:0;
  transform:translateY(20px);
  transition:opacity .6s ease, transform .6s ease;
}
.reveal-card.visible { opacity:1; transform:translateY(0); }

/* Reduced motion: respect user preference */
@media (prefers-reduced-motion: reduce) {
  .word-animate {
    animation: none !important;
    opacity: 1 !important;
    transform: none !important;
    filter: none !important;
  }
  .grid-line {
    animation: none !important;
    opacity: .2 !important;
    stroke-dashoffset: 0 !important;
  }
  .detail-dot {
    animation: none !important;
    opacity: .3 !important;
  }
  .corner-bracket {
    animation: none !important;
    opacity: 1 !important;
  }
  .float-dot {
    display: none;
  }
  .underline-accent::after {
    animation: none !important;
    width: 100% !important;
  }
  .reveal-card {
    opacity: 1 !important;
    transform: none !important;
    transition: none !important;
  }
}
</style>

<!-- ─── Global mouse gradient ─── -->
<div id="mouse-gradient" class="w-72 h-72 blur-3xl md:w-[420px] md:h-[420px]" aria-hidden="true"></div>

<!-- ─── Nav ─────────────────────────────────────────────── -->
<header>
  <nav id="landing-nav" class="fixed top-0 inset-x-0 z-50 transition-all duration-300" aria-label="Main navigation">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <a href="<?= url('/') ?>" class="flex items-center gap-2.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 rounded-lg" aria-label="Wareflow — go to homepage">
        <div class="w-8 h-8 bg-[#004ac6] rounded-[10px] flex items-center justify-center shadow-lg" aria-hidden="true">
          <span class="material-symbols-outlined text-white" style="font-size:17px">warehouse</span>
        </div>
        <span id="nav-logo" class="font-bold text-lg transition-colors duration-300">Wareflow</span>
      </a>
      <div class="flex items-center gap-3">
        <a href="#pricing" id="nav-pricing"
          class="text-sm font-medium transition-colors duration-300 hover:opacity-80 px-2 py-1 rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">Pricing</a>
        <a href="<?= url('/login') ?>" id="nav-signin"
          class="text-sm font-medium transition-colors duration-300 hover:opacity-80 px-2 py-1 rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">Sign in</a>
        <a href="<?= url('/register') ?>"
          class="bg-[#004ac6] text-white text-sm font-semibold px-5 py-2 rounded-[10px] hover:bg-[#0053db] transition-all shadow-lg hover:shadow-xl hover:shadow-blue-900/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-400">
          Get started free
        </a>
      </div>
    </div>
  </nav>
</header>

<main>

<!-- ─── Hero — Digital Serenity treatment ─────────────────── -->
<section id="hero" class="relative min-h-screen overflow-hidden flex flex-col justify-between"
  style="background:linear-gradient(135deg,#000b2e 0%,#000820 45%,#001050 100%)">

  <!-- SVG grid background -->
  <svg class="absolute inset-0 w-full h-full pointer-events-none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <defs>
      <pattern id="wf-grid" width="64" height="64" patternUnits="userSpaceOnUse">
        <path d="M 64 0 L 0 0 0 64" fill="none" stroke="rgba(59,130,246,.08)" stroke-width=".6"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#wf-grid)"/>
    <line x1="0" y1="20%" x2="100%" y2="20%" class="grid-line" style="animation-delay:.6s"/>
    <line x1="0" y1="80%" x2="100%" y2="80%" class="grid-line" style="animation-delay:1.1s"/>
    <line x1="20%" y1="0" x2="20%" y2="100%" class="grid-line" style="animation-delay:1.6s"/>
    <line x1="80%" y1="0" x2="80%" y2="100%" class="grid-line" style="animation-delay:2.1s"/>
    <line x1="50%" y1="0" x2="50%" y2="100%" class="grid-line" style="animation-delay:2.6s;opacity:.06"/>
    <line x1="0" y1="50%" x2="100%" y2="50%" class="grid-line" style="animation-delay:3.1s;opacity:.06"/>
    <circle cx="20%" cy="20%" r="2.5" class="detail-dot" style="animation-delay:3.2s"/>
    <circle cx="80%" cy="20%" r="2.5" class="detail-dot" style="animation-delay:3.4s"/>
    <circle cx="20%" cy="80%" r="2.5" class="detail-dot" style="animation-delay:3.6s"/>
    <circle cx="80%" cy="80%" r="2.5" class="detail-dot" style="animation-delay:3.8s"/>
    <circle cx="50%" cy="50%" r="2"   class="detail-dot" style="animation-delay:4.2s"/>
    <circle cx="50%" cy="20%" r="1.5" class="detail-dot" style="animation-delay:4.4s;fill:#93c5fd"/>
    <circle cx="20%" cy="50%" r="1.5" class="detail-dot" style="animation-delay:4.6s;fill:#93c5fd"/>
  </svg>

  <!-- Corner bracket decorations -->
  <div class="corner-bracket top-6 left-6 md:top-10 md:left-10 rounded-tl-sm" style="animation-delay:4.2s" aria-hidden="true">
    <div class="absolute top-0 left-0 w-2 h-2 bg-blue-300 opacity-30 rounded-full"></div>
  </div>
  <div class="corner-bracket top-6 right-6 md:top-10 md:right-10 rounded-tr-sm" style="animation-delay:4.4s" aria-hidden="true">
    <div class="absolute top-0 right-0 w-2 h-2 bg-blue-300 opacity-30 rounded-full"></div>
  </div>
  <div class="corner-bracket bottom-6 left-6 md:bottom-10 md:left-10 rounded-bl-sm" style="animation-delay:4.6s" aria-hidden="true">
    <div class="absolute bottom-0 left-0 w-2 h-2 bg-blue-300 opacity-30 rounded-full"></div>
  </div>
  <div class="corner-bracket bottom-6 right-6 md:bottom-10 md:right-10 rounded-br-sm" style="animation-delay:4.8s" aria-hidden="true">
    <div class="absolute bottom-0 right-0 w-2 h-2 bg-blue-300 opacity-30 rounded-full"></div>
  </div>

  <!-- Floating particles (activated on scroll) -->
  <div class="float-dot" style="top:22%;left:12%;animation-delay:.4s" aria-hidden="true"></div>
  <div class="float-dot" style="top:55%;left:88%;animation-delay:.9s" aria-hidden="true"></div>
  <div class="float-dot" style="top:38%;left:7%;animation-delay:1.4s" aria-hidden="true"></div>
  <div class="float-dot" style="top:72%;left:92%;animation-delay:1.9s" aria-hidden="true"></div>
  <div class="float-dot" style="top:15%;left:65%;animation-delay:2.4s" aria-hidden="true"></div>
  <div class="float-dot" style="top:82%;left:30%;animation-delay:2.9s" aria-hidden="true"></div>

  <!-- ── Top tagline ── -->
  <div class="relative z-10 text-center pt-36 md:pt-44">
    <div class="flex items-center justify-center gap-3 mb-4">
      <div class="w-8 h-px bg-gradient-to-r from-transparent to-blue-400 opacity-50" aria-hidden="true"></div>
      <p class="text-xs font-mono font-light text-blue-300 uppercase tracking-[.22em]">
        <span class="word-animate" data-delay="0">Private workspace.</span>
        <span class="word-animate" data-delay="160">Multi-warehouse.</span>
        <span class="word-animate" data-delay="320">Real-time.</span>
      </p>
      <div class="w-8 h-px bg-gradient-to-l from-transparent to-blue-400 opacity-50" aria-hidden="true"></div>
    </div>
  </div>

  <!-- ── Main headline ── -->
  <div class="relative z-10 text-center px-6 max-w-5xl mx-auto">
    <div class="absolute -left-8 top-1/2 -translate-y-1/2 w-5 h-px bg-blue-300 opacity-0" aria-hidden="true"
      style="animation:word-appear 1s ease-out forwards;animation-delay:3.4s"></div>
    <div class="absolute -right-8 top-1/2 -translate-y-1/2 w-5 h-px bg-blue-300 opacity-0" aria-hidden="true"
      style="animation:word-appear 1s ease-out forwards;animation-delay:3.6s"></div>

    <h1 class="font-extralight leading-tight tracking-tight text-white">
      <div class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl mb-3">
        <span class="word-animate" data-delay="300">Inventory</span>
        <span class="word-animate underline-accent" data-delay="400">ops,</span>
      </div>
      <div class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-thin text-blue-200 mb-6">
        <span class="word-animate" data-delay="500">under</span>
        <span class="word-animate" data-delay="600">control.</span>
      </div>
      <div class="text-base sm:text-lg md:text-xl font-light text-blue-300/80 max-w-2xl mx-auto leading-relaxed tracking-wide">
        <span class="word-animate" data-delay="750">Track</span>
        <span class="word-animate" data-delay="810">every</span>
        <span class="word-animate" data-delay="870">SKU.</span>
        <span class="word-animate" data-delay="950">Control</span>
        <span class="word-animate" data-delay="1010">every</span>
        <span class="word-animate" data-delay="1070">location.</span>
        <span class="word-animate" data-delay="1140">Move</span>
        <span class="word-animate" data-delay="1190">at</span>
        <span class="word-animate" data-delay="1240">the</span>
        <span class="word-animate" data-delay="1290">speed</span>
        <span class="word-animate" data-delay="1340">of</span>
        <span class="word-animate" data-delay="1390">your</span>
        <span class="word-animate" data-delay="1450">business.</span>
      </div>
    </h1>

    <!-- CTAs -->
    <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4 opacity-0"
      style="animation:word-appear 1s ease-out forwards;animation-delay:1.8s">
      <a href="<?= url('/register') ?>"
        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#004ac6] text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-[#0053db] shadow-xl shadow-blue-900/40 hover:shadow-2xl hover:shadow-blue-800/50 transition-all text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-blue-400">
        Start for free
        <span class="material-symbols-outlined" style="font-size:16px" aria-hidden="true">arrow_forward</span>
      </a>
      <a href="<?= url('/login') ?>"
        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-blue-400/30 text-blue-200 font-medium px-8 py-3.5 rounded-xl hover:border-blue-400/60 hover:text-white hover:bg-white/5 transition-all text-sm backdrop-blur-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
        <span class="material-symbols-outlined" style="font-size:16px" aria-hidden="true">login</span>
        Sign in to workspace
      </a>
    </div>

    <!-- Capability pills -->
    <div class="mt-10 flex flex-wrap items-center justify-center gap-6 opacity-0"
      style="animation:word-appear 1s ease-out forwards;animation-delay:2.2s">
      <?php $pills = [
        ['check_circle', 'Free plan available'],
        ['warehouse',    'Multi-warehouse'],
        ['group',        'Role-based access'],
        ['tune',         'Custom fields'],
      ];
      foreach ($pills as [$icon, $label]): ?>
      <div class="flex items-center gap-1.5 text-blue-300/70 text-xs">
        <span class="material-symbols-outlined" style="font-size:14px" aria-hidden="true"><?= $icon ?></span>
        <span><?= $label ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── Bottom tagline ── -->
  <div class="relative z-10 text-center pb-14 md:pb-20">
    <div class="mb-5 w-12 h-px bg-gradient-to-r from-transparent via-blue-300 to-transparent opacity-25 mx-auto" aria-hidden="true"></div>
    <p class="text-xs font-mono font-light text-blue-300/70 uppercase tracking-[.22em]">
      <span class="word-animate" data-delay="2000">Observe.</span>
      <span class="word-animate" data-delay="2100">Control.</span>
      <span class="word-animate" data-delay="2200">Grow.</span>
    </p>
    <div class="mt-8 flex flex-col items-center gap-1.5 opacity-0" aria-hidden="true"
      style="animation:word-appear 1s ease-out forwards;animation-delay:2.8s">
      <span class="text-blue-400/50 text-[10px] tracking-widest uppercase font-mono">scroll</span>
      <div class="w-px h-8 bg-gradient-to-b from-blue-400/40 to-transparent animate-pulse"></div>
    </div>
  </div>

</section>

<!-- ─── Features ──────────────────────────────────────────── -->
<section class="py-24 px-6 bg-white" id="features" aria-labelledby="features-heading">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-16 reveal-card">
      <p class="text-xs font-mono text-[#004ac6] uppercase tracking-widest mb-3">Everything your team needs</p>
      <h2 id="features-heading" class="text-3xl font-bold text-gray-900 mb-4 tracking-tight">Purpose-built for inventory teams</h2>
      <p class="text-gray-500 max-w-xl mx-auto">
        Every feature you need to track stock, record movements, and keep your team aligned. Nothing you don't.
      </p>
    </div>

    <!-- Bento grid — breaks the identical-card pattern -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-5">

      <!-- Inventory CRUD: primary feature, 4 of 6 columns -->
      <div class="reveal-card md:col-span-4 p-8 rounded-2xl border border-gray-100 bg-blue-50 hover:border-[#004ac6]/20 hover:shadow-lg transition-all duration-200 group"
        style="transition-delay:0ms">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-5 group-hover:scale-105 transition-transform" aria-hidden="true">
          <span class="material-symbols-outlined text-[#004ac6]" style="font-size:22px">inventory_2</span>
        </div>
        <h3 class="font-semibold text-gray-900 text-lg mb-2">Inventory CRUD</h3>
        <p class="text-sm text-gray-500 leading-relaxed max-w-md mb-5">Add, edit, search and filter thousands of SKUs with custom fields, status tracking, and bulk actions.</p>
        <ul class="space-y-2" aria-label="Included capabilities">
          <li class="flex items-center gap-2 text-xs text-gray-400">
            <span class="material-symbols-outlined text-[#004ac6]" style="font-size:14px" aria-hidden="true">check_circle</span>
            Bulk import and export
          </li>
          <li class="flex items-center gap-2 text-xs text-gray-400">
            <span class="material-symbols-outlined text-[#004ac6]" style="font-size:14px" aria-hidden="true">check_circle</span>
            Custom status tracking per SKU
          </li>
          <li class="flex items-center gap-2 text-xs text-gray-400">
            <span class="material-symbols-outlined text-[#004ac6]" style="font-size:14px" aria-hidden="true">check_circle</span>
            Configurable low-stock thresholds
          </li>
        </ul>
      </div>

      <!-- Multi-Warehouse: 2 of 6 columns -->
      <div class="reveal-card md:col-span-2 p-6 rounded-2xl border border-gray-100 bg-indigo-50 hover:border-indigo-200 hover:shadow-lg transition-all duration-200 group"
        style="transition-delay:60ms">
        <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform" aria-hidden="true">
          <span class="material-symbols-outlined text-indigo-600" style="font-size:20px">warehouse</span>
        </div>
        <h3 class="font-semibold text-gray-900 mb-2">Multi-Warehouse</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Manage unlimited warehouses with dedicated managers, location codes, capacity tracking, and utilization meters.</p>
      </div>

      <!-- Stock Movements: 2 of 6 columns -->
      <div class="reveal-card md:col-span-2 p-6 rounded-2xl border border-gray-100 bg-sky-50 hover:border-sky-200 hover:shadow-lg transition-all duration-200 group"
        style="transition-delay:120ms">
        <div class="w-11 h-11 bg-sky-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform" aria-hidden="true">
          <span class="material-symbols-outlined text-sky-600" style="font-size:20px">swap_horiz</span>
        </div>
        <h3 class="font-semibold text-gray-900 mb-2">Stock Movements</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Record inbound, outbound, transfer, and adjustment movements with a complete audit trail and reference codes.</p>
      </div>

      <!-- Custom Fields: 2 of 6 columns -->
      <div class="reveal-card md:col-span-2 p-6 rounded-2xl border border-gray-100 bg-violet-50 hover:border-violet-200 hover:shadow-lg transition-all duration-200 group"
        style="transition-delay:180ms">
        <div class="w-11 h-11 bg-violet-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform" aria-hidden="true">
          <span class="material-symbols-outlined text-violet-600" style="font-size:20px">tune</span>
        </div>
        <h3 class="font-semibold text-gray-900 mb-2">Custom Fields</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Build no-code custom attributes for any SKU: dropdowns, dates, numbers, checkboxes, and more.</p>
      </div>

      <!-- Users & RBAC: 2 of 6 columns -->
      <div class="reveal-card md:col-span-2 p-6 rounded-2xl border border-gray-100 bg-emerald-50 hover:border-emerald-200 hover:shadow-lg transition-all duration-200 group"
        style="transition-delay:240ms">
        <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-105 transition-transform" aria-hidden="true">
          <span class="material-symbols-outlined text-emerald-600" style="font-size:20px">group</span>
        </div>
        <h3 class="font-semibold text-gray-900 mb-2">Users &amp; RBAC</h3>
        <p class="text-sm text-gray-500 leading-relaxed">Invite teammates by email with role-based permissions: Owner, Admin, Manager, or Viewer. Invite links expire in 7 days with clear status for pending, accepted, and revoked requests.</p>
      </div>

      <!-- KPI Dashboard: full-width horizontal strip -->
      <div class="reveal-card md:col-span-6 p-6 rounded-2xl border border-gray-100 bg-amber-50 hover:border-amber-200 hover:shadow-lg transition-all duration-200 group flex flex-col sm:flex-row items-start sm:items-center gap-5"
        style="transition-delay:300ms">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform" aria-hidden="true">
          <span class="material-symbols-outlined text-amber-600" style="font-size:22px">dashboard</span>
        </div>
        <div class="flex-1 min-w-0">
          <h3 class="font-semibold text-gray-900 mb-1">KPI Dashboard</h3>
          <p class="text-sm text-gray-500 leading-relaxed">Live charts for inventory value, low-stock alerts, movement trends, and top categories, always up to date.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 flex-shrink-0 flex-wrap" aria-label="Dashboard metrics">
          <span class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-lg">Inventory value</span>
          <span class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-lg">Low-stock alerts</span>
          <span class="px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-lg">Movement trends</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ─── Pricing ──────────────────────────────────────────── -->
<section class="py-24 px-6 bg-white" id="pricing" aria-labelledby="pricing-heading">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-14 reveal-card">
      <p class="text-xs font-mono text-[#004ac6] uppercase tracking-widest mb-3">Transparent pricing</p>
      <h2 id="pricing-heading" class="text-3xl font-bold text-gray-900 mb-4 tracking-tight">Start free. Grow when you're ready.</h2>
      <p class="text-gray-500 max-w-md mx-auto">No credit card required to start. Upgrade your workspace when your team or inventory outgrows the free tier.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <!-- Starter -->
      <div class="reveal-card p-6 rounded-2xl border border-gray-100 bg-gray-50 flex flex-col group hover:border-[#004ac6]/20 hover:shadow-lg transition-all duration-200"
        style="transition-delay:0ms">
        <div class="mb-6">
          <p class="text-xs font-mono text-gray-400 uppercase tracking-widest mb-2">Starter</p>
          <p class="text-3xl font-bold text-gray-900">Free</p>
          <p class="text-sm text-gray-400 mt-1">No credit card needed</p>
        </div>
        <ul class="space-y-3 flex-1 mb-8" aria-label="Starter plan features">
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Up to 50 SKUs
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Up to 3 team members
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Multi-warehouse support
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            KPI dashboard
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Custom fields and full audit trail
          </li>
        </ul>
        <a href="<?= url('/register') ?>"
          class="w-full text-center text-sm font-semibold px-5 py-3 rounded-xl border border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#004ac6]">
          Get started free
        </a>
      </div>

      <!-- Pro — featured -->
      <div class="reveal-card p-6 rounded-2xl border-2 border-[#004ac6] bg-white shadow-xl shadow-blue-900/10 flex flex-col relative transition-all duration-200"
        style="transition-delay:80ms">
        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2">
          <span class="bg-[#004ac6] text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">Most popular</span>
        </div>
        <div class="mb-6">
          <p class="text-xs font-mono text-[#004ac6] uppercase tracking-widest mb-2">Pro</p>
          <div class="flex items-baseline gap-1">
            <p class="text-3xl font-bold text-gray-900">$5</p>
            <p class="text-sm text-gray-400">/month</p>
          </div>
          <p class="text-sm text-gray-400 mt-1">Per workspace, billed monthly</p>
        </div>
        <ul class="space-y-3 flex-1 mb-8" aria-label="Pro plan features">
          <li class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="material-symbols-outlined text-[#004ac6] flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Up to 500 SKUs
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="material-symbols-outlined text-[#004ac6] flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Up to 5 team members
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="material-symbols-outlined text-[#004ac6] flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Everything in Starter
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-700">
            <span class="material-symbols-outlined text-[#004ac6] flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Role-based invite controls
          </li>
        </ul>
        <a href="<?= url('/register') ?>"
          class="w-full text-center text-sm font-semibold px-5 py-3 rounded-xl bg-[#004ac6] text-white hover:bg-[#0053db] transition-colors shadow-lg shadow-blue-900/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#004ac6]">
          Start with Pro
        </a>
      </div>

      <!-- Max -->
      <div class="reveal-card p-6 rounded-2xl border border-gray-100 bg-gray-50 flex flex-col group hover:border-[#004ac6]/20 hover:shadow-lg transition-all duration-200"
        style="transition-delay:160ms">
        <div class="mb-6">
          <p class="text-xs font-mono text-gray-400 uppercase tracking-widest mb-2">Max</p>
          <div class="flex items-baseline gap-1">
            <p class="text-3xl font-bold text-gray-900">$10</p>
            <p class="text-sm text-gray-400">/month</p>
          </div>
          <p class="text-sm text-gray-400 mt-1">Per workspace, billed monthly</p>
        </div>
        <ul class="space-y-3 flex-1 mb-8" aria-label="Max plan features">
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Unlimited SKUs
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Up to 20 team members
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Everything in Pro
          </li>
          <li class="flex items-start gap-2.5 text-sm text-gray-600">
            <span class="material-symbols-outlined text-gray-400 flex-shrink-0 mt-px" style="font-size:15px" aria-hidden="true">check</span>
            Suitable for larger operations
          </li>
        </ul>
        <a href="<?= url('/register') ?>"
          class="w-full text-center text-sm font-semibold px-5 py-3 rounded-xl border border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#004ac6]">
          Start with Max
        </a>
      </div>

    </div>
  </div>
</section>

<!-- ─── CTA ───────────────────────────────────────────────── -->
<section class="py-24 px-6 relative overflow-hidden" style="background:linear-gradient(135deg,#001a5c,#004ac6,#0053db)">
  <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
    <svg class="w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <pattern id="cta-grid" width="56" height="56" patternUnits="userSpaceOnUse">
          <path d="M 56 0 L 0 0 0 56" fill="none" stroke="white" stroke-width=".5"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#cta-grid)"/>
    </svg>
  </div>
  <div class="relative max-w-xl mx-auto text-center reveal-card">
    <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 backdrop-blur-sm border border-white/20" aria-hidden="true">
      <span class="material-symbols-outlined text-white text-3xl">warehouse</span>
    </div>
    <h2 class="text-3xl font-bold text-white mb-4 tracking-tight">Ready to take control?</h2>
    <p class="text-blue-200 mb-10 leading-relaxed">
      Create your free workspace in under 60 seconds. No credit card required.
    </p>
    <a href="<?= url('/register') ?>"
      class="inline-flex items-center gap-2 bg-white text-[#004ac6] font-bold px-10 py-4 rounded-xl hover:bg-blue-50 transition-all shadow-2xl text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white">
      Create your workspace
      <span class="material-symbols-outlined" style="font-size:16px" aria-hidden="true">arrow_forward</span>
    </a>
    <p class="text-blue-300/60 text-xs mt-5">No credit card required. Upgrade when your team grows.</p>
  </div>
</section>

</main>

<!-- ─── Footer ─────────────────────────────────────────────── -->
<footer class="py-8 px-6 bg-white border-t border-gray-100 text-center text-sm text-gray-400">
  <div class="flex items-center justify-center gap-2 mb-2">
    <div class="w-6 h-6 bg-[#004ac6] rounded-lg flex items-center justify-center" aria-hidden="true">
      <span class="material-symbols-outlined text-white" style="font-size:14px">warehouse</span>
    </div>
    <span class="font-semibold text-gray-600">Wareflow</span>
  </div>
  &copy; <?= date('Y') ?> Wareflow &middot; Built with PHP &amp; Tailwind CSS
</footer>

<!-- ─── Vanilla JS — all Digital Serenity effects ──────────── -->
<script>
(function () {

  // 1. Word animations (replaces React useEffect + word-appear)
  setTimeout(function () {
    document.querySelectorAll('.word-animate').forEach(function (el) {
      var delay = parseInt(el.getAttribute('data-delay')) || 0;
      setTimeout(function () {
        el.style.animation = 'word-appear .85s ease-out forwards';
      }, delay);
    });
  }, 400);

  // Word hover glow
  document.querySelectorAll('.word-animate').forEach(function (el) {
    el.addEventListener('mouseenter', function () { this.style.textShadow = '0 0 22px rgba(147,197,253,.5)'; });
    el.addEventListener('mouseleave', function () { this.style.textShadow = 'none'; });
  });

  // 2. Mouse gradient tracking (replaces useState + mousemove useEffect)
  var grad = document.getElementById('mouse-gradient');
  document.addEventListener('mousemove', function (e) {
    grad.style.left    = e.clientX + 'px';
    grad.style.top     = e.clientY + 'px';
    grad.style.opacity = '1';
  });
  document.addEventListener('mouseleave', function () { grad.style.opacity = '0'; });

  // 3. Click ripple (replaces useState ripples + click useEffect)
  document.addEventListener('click', function (e) {
    var r = document.createElement('div');
    r.className = 'click-ripple';
    r.setAttribute('aria-hidden', 'true');
    r.style.left = e.clientX + 'px';
    r.style.top  = e.clientY + 'px';
    document.body.appendChild(r);
    setTimeout(function () { r.remove(); }, 800);
  });

  // 4. Floating particles — activate on scroll (replaces scroll useEffect)
  var particlesStarted = false;
  window.addEventListener('scroll', function () {
    if (particlesStarted) return;
    particlesStarted = true;
    document.querySelectorAll('.float-dot').forEach(function (el, i) {
      var baseDelay = parseFloat(el.style.animationDelay || '0') * 1000;
      setTimeout(function () {
        el.style.animationPlayState = 'running';
      }, baseDelay + i * 80);
    });
  });

  // 5. Nav style shift on scroll (transparent on hero → solid on light sections)
  var nav     = document.getElementById('landing-nav');
  var logo    = document.getElementById('nav-logo');
  var signin  = document.getElementById('nav-signin');
  var pricing = document.getElementById('nav-pricing');
  function updateNav() {
    var past = window.scrollY > (window.innerHeight * .85);
    if (past) {
      nav.style.background      = 'rgba(255,255,255,.92)';
      nav.style.borderBottom    = '1px solid #e2e8f0';
      nav.style.backdropFilter  = 'blur(12px)';
      logo.style.color          = '#004ac6';
      signin.style.color        = '#475569';
      pricing.style.color       = '#475569';
    } else {
      nav.style.background      = 'transparent';
      nav.style.borderBottom    = '1px solid transparent';
      nav.style.backdropFilter  = 'none';
      logo.style.color          = 'white';
      signin.style.color        = 'rgba(191,219,254,.9)';
      pricing.style.color       = 'rgba(191,219,254,.9)';
    }
  }
  updateNav();
  window.addEventListener('scroll', updateNav, { passive: true });

  // 6. Feature card reveal via Intersection Observer (scroll-triggered)
  if ('IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: .15 });
    document.querySelectorAll('.reveal-card').forEach(function (el) { obs.observe(el); });
  } else {
    document.querySelectorAll('.reveal-card').forEach(function (el) { el.classList.add('visible'); });
  }

})();
</script>
