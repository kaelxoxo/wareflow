<?php $title = 'Dashboard'; ?>

<style>
/* ── Entrance animations (Digital Serenity → dashboard) ── */
@keyframes slideUpFade {
  from { opacity:0; transform:translateY(18px); }
  to   { opacity:1; transform:translateY(0); }
}
@keyframes grid-draw-dash {
  from { stroke-dashoffset:600; opacity:0; }
  to   { stroke-dashoffset:0;   opacity:.18; }
}
@keyframes ripple-btn {
  0%   { transform:translate(-50%,-50%) scale(0); opacity:.5; }
  100% { transform:translate(-50%,-50%) scale(4); opacity:0; }
}

.kpi-card-anim {
  opacity:0;
  animation:slideUpFade .55s ease-out forwards;
}
.chart-anim {
  opacity:0;
  animation:slideUpFade .55s ease-out forwards;
}
.bottom-anim {
  opacity:0;
  animation:slideUpFade .55s ease-out forwards;
}

/* Ripple on buttons */
.btn-ripple { position:relative; overflow:hidden; }
.btn-ripple .ripple-inner {
  position:absolute; border-radius:50%;
  background:rgba(255,255,255,.35);
  pointer-events:none;
  animation:ripple-btn .55s ease-out forwards;
}
</style>

<!-- ── Page header with subtle animated grid ── -->
<div class="relative rounded-2xl overflow-hidden mb-xl"
  style="background:linear-gradient(135deg,#001a5c 0%,#003399 50%,#004ac6 100%);padding:28px 28px;">

  <!-- Mini SVG grid (same pattern as landing) -->
  <svg class="absolute inset-0 w-full h-full pointer-events-none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <pattern id="dash-grid" width="52" height="52" patternUnits="userSpaceOnUse">
        <path d="M 52 0 L 0 0 0 52" fill="none" stroke="rgba(255,255,255,.06)" stroke-width=".6"/>
      </pattern>
    </defs>
    <rect width="100%" height="100%" fill="url(#dash-grid)"/>
    <line x1="0" y1="50%" x2="100%" y2="50%" stroke="rgba(147,197,253,.15)" stroke-width=".6"
      stroke-dasharray="5 5" stroke-dashoffset="600"
      style="animation:grid-draw-dash 2s ease-out forwards;animation-delay:.5s"/>
    <line x1="50%" y1="0" x2="50%" y2="100%" stroke="rgba(147,197,253,.1)" stroke-width=".6"
      stroke-dasharray="5 5" stroke-dashoffset="600"
      style="animation:grid-draw-dash 2s ease-out forwards;animation-delay:.9s"/>
    <!-- Decorative corner dots -->
    <circle cx="5" cy="5" r="2" fill="rgba(147,197,253,.3)" opacity="0"
      style="animation:slideUpFade .8s ease-out forwards;animation-delay:1.4s"/>
    <circle cx="calc(100% - 5px)" cy="5" r="2" fill="rgba(147,197,253,.3)" opacity="0"
      style="animation:slideUpFade .8s ease-out forwards;animation-delay:1.6s"/>
  </svg>

  <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
    <div>
      <h1 class="text-[22px] font-bold text-white tracking-tight leading-tight">
        Welcome back, <span class="text-blue-200"><?= e(Auth::user()['name']) ?></span>
      </h1>
      <p class="text-sm text-blue-200/60 mt-1"><?= number_format($kpis['total_items']) ?> SKUs across <?= $kpis['active_warehouses'] ?> warehouse<?= $kpis['active_warehouses'] != 1 ? 's' : '' ?></p>
    </div>
    <div class="flex items-center gap-sm">
      <?php if ($kpis['low_stock'] > 0): ?>
      <a href="<?= url('/inventory?low_stock=1') ?>" class="flex items-center gap-xs bg-red-500/20 text-red-200 border border-red-400/30 px-3 py-1.5 rounded-lg text-xs font-semibold backdrop-blur-sm hover:bg-red-500/30 transition-colors">
        <span class="material-symbols-outlined" style="font-size:14px">warning</span>
        <?= $kpis['low_stock'] ?> low stock alert<?= $kpis['low_stock'] > 1 ? 's' : '' ?>
      </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── KPI Grid — staggered entrance ── -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-md mb-xl">
  <?php
  $kpiCards = [
    ['inventory_2', 'bg-[#eff4ff]', 'text-primary',     'Total Items',       number_format($kpis['total_items']),   'SKUs tracked',        false],
    ['warning',     'bg-red-50',    'text-error',        'Low Stock',         $kpis['low_stock'],                     'Need reorder',        $kpis['low_stock'] > 0],
    ['payments',    'bg-violet-50', 'text-violet-600',   'Inventory Value',   money($kpis['inventory_value']),        'Total stock value',   false],
    ['warehouse',   'bg-emerald-50','text-emerald-600',  'Warehouses',        $kpis['active_warehouses'],             'Active locations',    false],
  ];
  foreach ($kpiCards as $i => [$icon, $ibg, $ic, $label, $value, $sub, $alert]): ?>
  <div class="card kpi-card-anim group hover:shadow-md"
    style="animation-delay:<?= $i * 90 ?>ms">
    <div class="flex items-start justify-between mb-md">
      <div class="w-10 h-10 <?= $ibg ?> rounded-xl flex items-center justify-center flex-shrink-0
        group-hover:scale-105 transition-transform duration-150">
        <span class="material-symbols-outlined <?= $ic ?>" style="font-size:19px"><?= $icon ?></span>
      </div>
      <?php if ($alert): ?>
        <span class="badge bg-red-100 text-red-700 text-[10px]">Action needed</span>
      <?php endif; ?>
    </div>
    <p class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider"><?= $label ?></p>
    <p class="text-[27px] font-bold mt-1 text-on-surface leading-none tracking-tight kpi-value"><?= $value ?></p>
    <p class="text-xs text-on-surface-variant mt-1.5"><?= $sub ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- ── Charts row — staggered ── -->
<div class="grid lg:grid-cols-3 gap-md mb-xl">

  <!-- Trend chart -->
  <div class="lg:col-span-2 card chart-anim" style="animation-delay:380ms">
    <div class="flex items-center justify-between mb-lg">
      <div>
        <h3 class="font-semibold text-on-surface text-[15px]">Stock Movement Trend</h3>
        <p class="text-xs text-on-surface-variant mt-0.5">Last 7 days — quantity in / out</p>
      </div>
      <a href="<?= url('/stock') ?>" class="text-xs text-primary font-semibold hover:underline flex items-center gap-xs">
        View all <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
      </a>
    </div>
    <canvas id="trendChart" height="120"></canvas>
    <script>window._trendData = <?= json_encode(array_values($trend)) ?>;</script>
  </div>

  <!-- Top categories -->
  <div class="card chart-anim" style="animation-delay:460ms">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-on-surface text-[15px]">Top Categories</h3>
      <a href="<?= url('/settings?tab=categories') ?>" class="text-xs text-primary font-semibold hover:underline">Manage</a>
    </div>
    <?php if (empty($topCategories)): ?>
      <div class="text-center py-8">
        <span class="material-symbols-outlined text-on-surface-variant text-4xl">label_off</span>
        <p class="text-sm text-on-surface-variant mt-2">No categories yet.</p>
        <a href="<?= url('/settings?tab=categories') ?>" class="text-xs text-primary font-semibold hover:underline mt-2 inline-block">Add one →</a>
      </div>
    <?php else:
      $maxCnt  = max(array_column($topCategories, 'cnt') ?: [1]);
      $palette = ['bg-primary','bg-violet-500','bg-emerald-500','bg-amber-500','bg-rose-500'];
      foreach ($topCategories as $i => $cat):
        $pct = $maxCnt ? round($cat['cnt'] / $maxCnt * 100) : 0;
    ?>
    <div class="mb-3">
      <div class="flex justify-between text-[13px] mb-1.5">
        <span class="font-medium text-on-surface truncate max-w-[140px]"><?= e($cat['name']) ?></span>
        <span class="font-semibold text-on-surface ml-2 flex-shrink-0"><?= $cat['cnt'] ?></span>
      </div>
      <div class="h-1.5 bg-surface-container rounded-full overflow-hidden">
        <div class="h-full rounded-full <?= $palette[$i % 5] ?> transition-all duration-700"
          style="width:0" data-width="<?= $pct ?>%"></div>
      </div>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div>

<!-- ── Bottom row — staggered ── -->
<div class="grid lg:grid-cols-2 gap-md">

  <!-- Low stock -->
  <div class="card bottom-anim" style="animation-delay:540ms">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-on-surface text-[15px] flex items-center gap-xs">
        <span class="material-symbols-outlined text-error" style="font-size:17px">warning</span>
        Low Stock Alerts
      </h3>
      <a href="<?= url('/inventory?low_stock=1') ?>" class="text-xs text-primary font-semibold hover:underline flex items-center gap-xs">
        View all <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
      </a>
    </div>
    <?php if (empty($lowStockItems)): ?>
      <div class="text-center py-10">
        <div class="w-14 h-14 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-3">
          <span class="material-symbols-outlined text-emerald-500 text-3xl">check_circle</span>
        </div>
        <p class="text-sm font-medium text-on-surface">All good!</p>
        <p class="text-xs text-on-surface-variant mt-1">All items are well stocked.</p>
      </div>
    <?php else: ?>
      <div class="divide-y divide-outline-variant">
        <?php foreach ($lowStockItems as $item): ?>
        <div class="flex items-center gap-sm py-[11px]">
          <div class="flex-1 min-w-0">
            <p class="text-[13px] font-semibold text-on-surface truncate"><?= e($item['name']) ?></p>
            <p class="text-[11px] text-on-surface-variant font-mono mt-0.5"><?= e($item['sku']) ?></p>
          </div>
          <div class="text-right mr-1">
            <p class="text-[13px] font-bold text-error"><?= $item['quantity'] ?> <span class="font-normal text-on-surface-variant"><?= e($item['unit']) ?></span></p>
            <p class="text-[11px] text-on-surface-variant">min <?= $item['reorder_point'] ?></p>
          </div>
          <a href="<?= url('/inventory/'.$item['id'].'/edit') ?>"
            class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-low transition-colors">
            <span class="material-symbols-outlined" style="font-size:15px">edit</span>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Recent movements -->
  <div class="card bottom-anim" style="animation-delay:620ms">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-on-surface text-[15px]">Recent Movements</h3>
      <a href="<?= url('/stock') ?>" class="text-xs text-primary font-semibold hover:underline flex items-center gap-xs">
        View all <span class="material-symbols-outlined" style="font-size:14px">arrow_forward</span>
      </a>
    </div>
    <?php if (empty($recentMovements)): ?>
      <div class="text-center py-10">
        <div class="w-14 h-14 bg-surface-container rounded-full flex items-center justify-center mx-auto mb-3">
          <span class="material-symbols-outlined text-on-surface-variant text-3xl">history</span>
        </div>
        <p class="text-sm font-medium text-on-surface">No movements yet</p>
        <p class="text-xs text-on-surface-variant mt-1">Stock changes will appear here.</p>
      </div>
    <?php else: ?>
      <div class="divide-y divide-outline-variant">
        <?php foreach ($recentMovements as $m):
          $tc   = ['in'=>'bg-emerald-50 text-emerald-700','out'=>'bg-red-50 text-red-700','transfer'=>'bg-blue-50 text-blue-700','adjustment'=>'bg-violet-50 text-violet-700'][$m['movement_type']] ?? 'bg-gray-50 text-gray-600';
          $qCls = $m['movement_type'] === 'out' ? 'text-error' : 'text-emerald-600';
          $sign = $m['movement_type'] === 'out' ? '-' : '+';
        ?>
        <div class="flex items-center gap-sm py-[11px]">
          <span class="badge <?= $tc ?> text-[10px] uppercase flex-shrink-0"><?= $m['movement_type'] ?></span>
          <div class="flex-1 min-w-0">
            <p class="text-[13px] font-medium text-on-surface truncate"><?= e($m['item_name']) ?></p>
            <p class="text-[11px] text-on-surface-variant"><?= e($m['user_name']) ?> · <?= ago($m['created_at']) ?></p>
          </div>
          <span class="text-[13px] font-bold <?= $qCls ?>"><?= $sign ?><?= $m['quantity'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- ── Chart.js ── -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  /* Trend chart */
  var raw    = window._trendData || [];
  var isDark = document.getElementById('html-root').classList.contains('dark');
  var grid   = isDark ? 'rgba(255,255,255,.04)' : 'rgba(0,0,0,.04)';
  var tick   = isDark ? '#64748b' : '#94a3b8';
  var legend = isDark ? '#94a3b8' : '#475569';

  var days = [];
  for (var i = 6; i >= 0; i--) {
    var d = new Date(); d.setDate(d.getDate() - i);
    days.push(d.toISOString().slice(0, 10));
  }
  var indexed = {};
  raw.forEach(function (r) { indexed[r.d] = r; });

  var labels  = days.map(function (d) {
    var dt = new Date(d + 'T00:00:00');
    return dt.toLocaleDateString('en', { month: 'short', day: 'numeric' });
  });
  var inData  = days.map(function (d) { return +(indexed[d] && indexed[d].stock_in  || 0); });
  var outData = days.map(function (d) { return +(indexed[d] && indexed[d].stock_out || 0); });

  new Chart(document.getElementById('trendChart'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        { label:'Stock In',  data:inData,  backgroundColor:isDark?'rgba(96,165,250,.75)':'rgba(0,74,198,.75)',  borderRadius:5, borderSkipped:false },
        { label:'Stock Out', data:outData, backgroundColor:isDark?'rgba(248,113,113,.65)':'rgba(220,38,38,.6)', borderRadius:5, borderSkipped:false },
      ],
    },
    options: {
      responsive:true, maintainAspectRatio:true,
      plugins: {
        legend: {
          position:'bottom',
          labels:{ font:{ family:'Plus Jakarta Sans', size:11, weight:'500' }, color:legend, boxWidth:12, padding:20 },
        },
        tooltip: {
          backgroundColor: isDark?'#162032':'#0f172a',
          titleColor:'#e2e8f0', bodyColor:'#94a3b8',
          padding:10, cornerRadius:8,
          callbacks:{ label:function(ctx){ return ' '+ctx.dataset.label+': '+ctx.parsed.y+' units'; } },
        },
      },
      scales: {
        x:{ grid:{display:false}, border:{display:false}, ticks:{color:tick, font:{family:'Plus Jakarta Sans',size:11}} },
        y:{ beginAtZero:true, grid:{color:grid}, border:{display:false}, ticks:{color:tick, font:{family:'Plus Jakarta Sans',size:11}, precision:0} },
      },
    },
  });

  /* Animate category progress bars after paint */
  requestAnimationFrame(function () {
    setTimeout(function () {
      document.querySelectorAll('[data-width]').forEach(function (el) {
        el.style.width = el.getAttribute('data-width');
      });
    }, 600);
  });

  /* Button ripple effect */
  document.querySelectorAll('.btn-ripple').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      var rect = btn.getBoundingClientRect();
      var r = document.createElement('span');
      r.className = 'ripple-inner';
      r.style.left   = (e.clientX - rect.left) + 'px';
      r.style.top    = (e.clientY - rect.top)  + 'px';
      r.style.width  = '10px';
      r.style.height = '10px';
      btn.appendChild(r);
      setTimeout(function () { r.remove(); }, 560);
    });
  });
})();
</script>
