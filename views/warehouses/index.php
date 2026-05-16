<?php $title = 'Warehouses'; ?>
<div class="flex items-center justify-between mb-lg">
  <div>
    <h1 class="text-2xl font-bold text-on-surface">Warehouses</h1>
    <p class="text-sm text-on-surface-variant mt-0.5"><?= count($warehouses) ?> location<?= count($warehouses)!==1?'s':'' ?></p>
  </div>
  <?php if (Auth::can('manage_warehouses')): ?>
  <button onclick="openModal('createModal')" class="btn-primary flex items-center gap-xs text-sm">
    <span class="material-symbols-outlined text-[18px]">add</span> New Warehouse
  </button>
  <?php endif; ?>
</div>

<!-- Warehouse cards -->
<?php if (empty($warehouses)): ?>
<div class="card text-center py-16">
  <span class="material-symbols-outlined text-5xl text-on-surface-variant">warehouse</span>
  <p class="text-on-surface-variant mt-2">No warehouses yet.</p>
  <?php if (Auth::can('manage_warehouses')): ?>
  <button onclick="openModal('createModal')" class="btn-primary inline-flex items-center gap-xs mt-4 text-sm">
    <span class="material-symbols-outlined text-[16px]">add</span> Create warehouse
  </button>
  <?php endif; ?>
</div>
<?php else: ?>
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-lg">
  <?php foreach ($warehouses as $wh):
    $utilPct = $wh['capacity'] > 0 ? min(100, round($wh['total_items'] / $wh['capacity'] * 100)) : 0;
  ?>
  <div class="card hover:shadow-md transition-all">
    <div class="flex items-start justify-between mb-md">
      <div class="flex items-center gap-sm">
        <div class="w-10 h-10 bg-[#eff4ff] rounded-xl flex items-center justify-center">
          <span class="material-symbols-outlined text-primary">warehouse</span>
        </div>
        <div>
          <h3 class="font-semibold text-on-surface"><?= e($wh['name']) ?></h3>
          <?php if ($wh['code']): ?>
          <p class="text-xs text-on-surface-variant font-mono"><?= e($wh['code']) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <?= status_badge($wh['status']) ?>
    </div>

    <?php if ($wh['location']): ?>
    <p class="text-sm text-on-surface-variant flex items-center gap-xs mb-md">
      <span class="material-symbols-outlined text-[14px]">location_on</span> <?= e($wh['location']) ?>
    </p>
    <?php endif; ?>

    <div class="grid grid-cols-2 gap-sm mb-md">
      <div class="bg-surface-container-low p-sm rounded-lg">
        <p class="text-xs text-on-surface-variant">Items</p>
        <p class="font-semibold text-on-surface"><?= number_format($wh['total_items']) ?></p>
      </div>
      <div class="bg-surface-container-low p-sm rounded-lg">
        <p class="text-xs text-on-surface-variant">Value</p>
        <p class="font-semibold text-on-surface"><?= money($wh['total_value']) ?></p>
      </div>
    </div>

    <?php if ($wh['capacity'] > 0): ?>
    <div class="mb-md">
      <div class="flex justify-between text-xs text-on-surface-variant mb-1">
        <span>Capacity</span><span><?= $utilPct ?>%</span>
      </div>
      <div class="h-1.5 bg-surface-container rounded-full">
        <div class="h-full rounded-full <?= $utilPct>80?'bg-error':($utilPct>60?'bg-amber-500':'bg-primary') ?>"
          style="width:<?= $utilPct ?>%"></div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($wh['manager_name']): ?>
    <p class="text-xs text-on-surface-variant flex items-center gap-xs mb-md">
      <span class="material-symbols-outlined text-[14px]">person</span> <?= e($wh['manager_name']) ?>
    </p>
    <?php endif; ?>

    <?php if (Auth::can('manage_warehouses')): ?>
    <div class="flex gap-sm pt-md border-t border-outline-variant">
      <button onclick="openEditModal(<?= htmlspecialchars(json_encode($wh), ENT_QUOTES) ?>)"
        class="flex-1 btn-secondary text-sm py-2">Edit</button>
      <form method="POST" action="<?= url('/warehouses/'.$wh['id'].'/delete') ?>"
        onsubmit="return confirm('Delete <?= e(addslashes($wh['name'])) ?>?')">
        <?= csrf_field() ?>
        <button type="submit" class="px-sm py-2 text-on-surface-variant hover:text-error hover:bg-red-50 rounded-lg transition-colors">
          <span class="material-symbols-outlined text-[18px]">delete</span>
        </button>
      </form>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Create Modal -->
<div id="createModal" class="modal-backdrop hidden">
  <div class="modal animate-in w-full max-w-lg">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-lg">New Warehouse</h3>
      <button onclick="closeModal('createModal')" class="text-on-surface-variant hover:text-on-surface">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="<?= url('/warehouses') ?>">
      <?= csrf_field() ?>
      <?php include __DIR__ . '/../../views/warehouses/_form.php'; ?>
      <div class="flex gap-md justify-end mt-xl">
        <button type="button" onclick="closeModal('createModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Create Warehouse</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-backdrop hidden">
  <div class="modal animate-in w-full max-w-lg">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-lg">Edit Warehouse</h3>
      <button onclick="closeModal('editModal')" class="text-on-surface-variant hover:text-on-surface">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form id="editForm" method="POST">
      <?= csrf_field() ?>
      <?php $editMode = true; include __DIR__ . '/../../views/warehouses/_form.php'; ?>
      <div class="flex gap-md justify-end mt-xl">
        <button type="button" onclick="closeModal('editModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(wh) {
  const f = document.getElementById('editForm');
  f.action = '<?= url('/warehouses/') ?>' + wh.id + '/update';
  f.name.value     = wh.name;
  f.code.value     = wh.code||'';
  f.location.value = wh.location||'';
  f.capacity.value = wh.capacity||0;
  f.status.value   = wh.status;
  if (f.manager_id) f.manager_id.value = wh.manager_id||'';
  openModal('editModal');
}
</script>
