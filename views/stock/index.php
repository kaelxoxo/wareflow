<?php $title = 'Stock Movements'; ?>
<div class="flex items-center justify-between mb-lg">
  <div>
    <h1 class="text-2xl font-bold text-on-surface">Stock Movements</h1>
    <p class="text-sm text-on-surface-variant mt-0.5">Full audit trail of all inventory changes</p>
  </div>
  <?php if (Auth::can('manage_inventory')): ?>
  <button onclick="openModal('transferModal')" class="btn-primary flex items-center gap-xs text-sm">
    <span class="material-symbols-outlined text-[18px]">swap_horiz</span> New Movement
  </button>
  <?php endif; ?>
</div>

<!-- Summary stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-lg mb-lg">
  <?php
  $statCards = [
    ['total_in',       'Stock In',       'arrow_downward', 'text-emerald-600 bg-emerald-50'],
    ['total_out',      'Stock Out',      'arrow_upward',   'text-red-600 bg-red-50'],
    ['total_transfer', 'Transfers',      'swap_horiz',     'text-blue-600 bg-blue-50'],
    ['total_movements','Total Movements','history',         'text-on-surface-variant bg-surface-container'],
  ];
  foreach ($statCards as [$key, $label, $icon, $cls]): ?>
  <div class="card">
    <div class="flex items-center gap-sm mb-xs">
      <span class="material-symbols-outlined p-1.5 rounded-lg text-[18px] <?= $cls ?>"><?= $icon ?></span>
      <span class="text-xs text-on-surface-variant"><?= $label ?></span>
    </div>
    <p class="text-2xl font-bold"><?= number_format($stats[$key] ?? 0) ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filters -->
<div class="card mb-lg">
  <form method="GET" class="flex flex-wrap gap-md items-end">
    <div>
      <label class="block text-xs font-medium text-on-surface-variant mb-1">Type</label>
      <select name="type" class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary">
        <option value="">All types</option>
        <option value="in"         <?= $filters['type']==='in'?'selected':'' ?>>Stock In</option>
        <option value="out"        <?= $filters['type']==='out'?'selected':'' ?>>Stock Out</option>
        <option value="transfer"   <?= $filters['type']==='transfer'?'selected':'' ?>>Transfer</option>
        <option value="adjustment" <?= $filters['type']==='adjustment'?'selected':'' ?>>Adjustment</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-medium text-on-surface-variant mb-1">Warehouse</label>
      <select name="warehouse_id" class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary">
        <option value="">All warehouses</option>
        <?php foreach ($warehouses as $w): ?>
        <option value="<?= $w['id'] ?>" <?= $filters['warehouse_id']==$w['id']?'selected':'' ?>><?= e($w['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs font-medium text-on-surface-variant mb-1">From date</label>
      <input type="date" name="date_from" value="<?= e($filters['date_from']) ?>"
        class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary"/>
    </div>
    <div>
      <label class="block text-xs font-medium text-on-surface-variant mb-1">To date</label>
      <input type="date" name="date_to" value="<?= e($filters['date_to']) ?>"
        class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary"/>
    </div>
    <button type="submit" class="btn-primary text-sm">Filter</button>
    <a href="<?= url('/stock') ?>" class="btn-secondary text-sm">Reset</a>
  </form>
</div>

<!-- Table -->
<div class="card p-0 overflow-hidden">
  <?php if (empty($pagination['data'])): ?>
  <div class="text-center py-16">
    <span class="material-symbols-outlined text-5xl text-on-surface-variant">history</span>
    <p class="text-on-surface-variant mt-2">No movements found.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-surface-container-low border-b border-outline-variant">
        <tr>
          <th class="text-left px-lg py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Date</th>
          <th class="text-left px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Item</th>
          <th class="text-center px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Type</th>
          <th class="text-left px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide hidden lg:table-cell">From → To</th>
          <th class="text-right px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Qty</th>
          <th class="text-left px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide hidden md:table-cell">Reference</th>
          <th class="text-left px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide hidden md:table-cell">By</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant">
        <?php foreach ($pagination['data'] as $m):
          $typeColors = [
            'in'         => 'bg-emerald-100 text-emerald-800',
            'out'        => 'bg-red-100 text-red-700',
            'transfer'   => 'bg-blue-100 text-blue-700',
            'adjustment' => 'bg-purple-100 text-purple-700',
          ];
          $tc = $typeColors[$m['movement_type']] ?? 'bg-gray-100 text-gray-600';
        ?>
        <tr class="table-row">
          <td class="px-lg py-sm">
            <p class="text-sm"><?= date('M j, Y', strtotime($m['created_at'])) ?></p>
            <p class="text-xs text-on-surface-variant"><?= date('H:i', strtotime($m['created_at'])) ?></p>
          </td>
          <td class="px-md py-sm">
            <p class="font-medium"><?= e($m['item_name']) ?></p>
            <p class="text-xs text-on-surface-variant font-mono"><?= e($m['sku']) ?></p>
          </td>
          <td class="px-md py-sm text-center">
            <span class="badge <?= $tc ?>"><?= ucfirst($m['movement_type']) ?></span>
          </td>
          <td class="px-md py-sm hidden lg:table-cell text-on-surface-variant">
            <?= e($m['from_warehouse'] ?? '—') ?> → <?= e($m['to_warehouse'] ?? '—') ?>
          </td>
          <td class="px-md py-sm text-right font-semibold <?= in_array($m['movement_type'],['out']) ? 'text-error' : 'text-emerald-600' ?>">
            <?= $m['movement_type']==='out' ? '-' : '+' ?><?= number_format($m['quantity']) ?>
          </td>
          <td class="px-md py-sm hidden md:table-cell text-on-surface-variant">
            <?= e($m['reference'] ?: '—') ?>
          </td>
          <td class="px-md py-sm hidden md:table-cell text-on-surface-variant">
            <?= e($m['user_name']) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pagination['last_page'] > 1): ?>
  <div class="flex items-center justify-between px-lg py-sm border-t border-outline-variant">
    <p class="text-sm text-on-surface-variant">Showing <?= count($pagination['data']) ?> of <?= $pagination['total'] ?></p>
    <?= paginate_links($pagination) ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Transfer Modal -->
<?php if (Auth::can('manage_inventory')): ?>
<div id="transferModal" class="modal-backdrop hidden">
  <div class="modal animate-in w-full max-w-lg">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-lg">Record Stock Movement</h3>
      <button onclick="closeModal('transferModal')" class="text-on-surface-variant hover:text-on-surface">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="<?= url('/stock/transfer') ?>">
      <?= csrf_field() ?>
      <div class="space-y-md">
        <div>
          <label class="block text-sm font-medium mb-1.5">Item <span class="text-error">*</span></label>
          <select name="item_id" required class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <option value="">— Select item —</option>
            <?php foreach ($items as $it): ?>
            <option value="<?= $it['id'] ?>"><?= e($it['name']) ?> (<?= e($it['sku']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Movement Type <span class="text-error">*</span></label>
          <select name="movement_type" id="movType" required onchange="updateMovForm(this.value)"
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <option value="">— Select type —</option>
            <option value="in">Stock In (Receiving)</option>
            <option value="out">Stock Out (Dispatch)</option>
            <option value="transfer">Transfer Between Warehouses</option>
            <option value="adjustment">Quantity Adjustment</option>
          </select>
        </div>
        <div id="qtyField">
          <label class="block text-sm font-medium mb-1.5">Quantity <span class="text-error">*</span></label>
          <input type="number" name="quantity" min="1" value="1"
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
        </div>
        <div id="newQtyField" class="hidden">
          <label class="block text-sm font-medium mb-1.5">New Quantity</label>
          <input type="number" name="new_quantity" min="0" value="0"
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
        </div>
        <div id="fromWarehouseField">
          <label class="block text-sm font-medium mb-1.5">From Warehouse</label>
          <select name="from_warehouse_id" class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <option value="">— None —</option>
            <?php foreach ($warehouses as $w): ?>
            <option value="<?= $w['id'] ?>"><?= e($w['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div id="toWarehouseField">
          <label class="block text-sm font-medium mb-1.5">To Warehouse</label>
          <select name="to_warehouse_id" class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <option value="">— None —</option>
            <?php foreach ($warehouses as $w): ?>
            <option value="<?= $w['id'] ?>"><?= e($w['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Reference</label>
          <input type="text" name="reference" placeholder="PO-12345, REF-001…"
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Notes</label>
          <textarea name="notes" rows="2" placeholder="Optional notes…"
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary resize-none"></textarea>
        </div>
      </div>
      <div class="flex gap-md justify-end mt-xl">
        <button type="button" onclick="closeModal('transferModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Record Movement</button>
      </div>
    </form>
  </div>
</div>
<script>
function updateMovForm(type) {
  const isAdj  = type === 'adjustment';
  const isIn   = type === 'in';
  const isOut  = type === 'out';
  const isTrans= type === 'transfer';
  document.getElementById('qtyField').classList.toggle('hidden', isAdj);
  document.getElementById('newQtyField').classList.toggle('hidden', !isAdj);
  document.getElementById('fromWarehouseField').classList.toggle('hidden', isIn);
  document.getElementById('toWarehouseField').classList.toggle('hidden', isOut && !isTrans);
}
</script>
<?php endif; ?>
