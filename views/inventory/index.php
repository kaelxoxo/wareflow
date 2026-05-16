<?php $title = 'Inventory'; ?>
<div class="flex items-center justify-between mb-lg">
  <div>
    <h1 class="text-2xl font-bold text-on-surface">Inventory</h1>
    <p class="text-sm text-on-surface-variant mt-0.5">
      <?= number_format($pagination['total']) ?> items · Page <?= $pagination['current_page'] ?> of <?= $pagination['last_page'] ?>
    </p>
  </div>
  <?php if (Auth::can('manage_inventory')): ?>
  <a href="<?= url('/inventory/create') ?>" class="btn-primary flex items-center gap-xs">
    <span class="material-symbols-outlined text-[18px]">add</span> Add Item
  </a>
  <?php endif; ?>
</div>

<!-- Filters -->
<div class="card mb-lg">
  <form method="GET" action="<?= url('/inventory') ?>" class="flex flex-wrap gap-md items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-[11px] font-semibold uppercase tracking-[0.05em] text-on-surface-variant mb-1.5">Search</label>
      <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[16px]">search</span>
        <input type="text" name="search" value="<?= e($filters['search']) ?>" placeholder="Name or SKU…"
          class="w-full border border-outline-variant rounded-lg pl-8 pr-3 py-2 text-sm bg-surface-container-low focus:border-primary"/>
      </div>
    </div>
    <div>
      <label class="block text-[11px] font-semibold uppercase tracking-[0.05em] text-on-surface-variant mb-1.5">Category</label>
      <select name="category_id" class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary">
        <option value="">All categories</option>
        <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $filters['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[11px] font-semibold uppercase tracking-[0.05em] text-on-surface-variant mb-1.5">Warehouse</label>
      <select name="warehouse_id" class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary">
        <option value="">All warehouses</option>
        <?php foreach ($warehouses as $w): ?>
        <option value="<?= $w['id'] ?>" <?= $filters['warehouse_id']==$w['id']?'selected':'' ?>><?= e($w['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-[11px] font-semibold uppercase tracking-[0.05em] text-on-surface-variant mb-1.5">Status</label>
      <select name="status" class="border border-outline-variant rounded-lg px-3 py-2 text-sm bg-surface-container-low focus:border-primary">
        <option value="">All statuses</option>
        <option value="active"       <?= $filters['status']==='active'?'selected':'' ?>>Active</option>
        <option value="inactive"     <?= $filters['status']==='inactive'?'selected':'' ?>>Inactive</option>
        <option value="discontinued" <?= $filters['status']==='discontinued'?'selected':'' ?>>Discontinued</option>
      </select>
    </div>
    <div class="flex items-center gap-xs">
      <label class="flex items-center gap-xs text-[13.5px] cursor-pointer select-none">
        <input type="checkbox" name="low_stock" value="1" <?= $filters['low_stock']?'checked':'' ?> class="rounded border-outline-variant text-primary">
        <span>Low stock only</span>
      </label>
    </div>
    <button type="submit" class="btn-primary text-sm">Filter</button>
    <a href="<?= url('/inventory') ?>" class="btn-secondary text-sm">Reset</a>
  </form>
</div>

<?php if (!empty($fuzzyMode ?? false) && !empty($fuzzyQuery ?? '')): ?>
<!-- Fuzzy match notice -->
<div class="mb-lg flex items-start gap-sm px-md py-3 bg-amber-50 border border-amber-200 rounded-xl dark:bg-amber-900/10 dark:border-amber-700/30">
  <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 flex-shrink-0" style="font-size:17px;margin-top:1px">auto_fix_high</span>
  <p class="text-sm text-amber-800 dark:text-amber-300">
    No exact match for <strong class="font-semibold">"<?= e($fuzzyQuery) ?>"</strong>.
    Showing closest results by name similarity.
    <a href="<?= url('/inventory') ?>" class="font-semibold underline underline-offset-2 ml-1 hover:text-amber-900">Clear search</a>
  </p>
</div>
<?php endif; ?>

<!-- Table -->
<div class="card p-0 overflow-hidden">
  <?php if (empty($pagination['data'])): ?>
  <?php $hasActiveFilters = !empty($filters['search']) || !empty($filters['category_id'])
      || !empty($filters['warehouse_id']) || !empty($filters['status']) || !empty($filters['low_stock']); ?>
  <div class="text-center py-14 px-6">
    <div class="w-12 h-12 bg-surface-container-low rounded-xl flex items-center justify-center mx-auto mb-4">
      <span class="material-symbols-outlined text-on-surface-variant" style="font-size:22px"><?= $hasActiveFilters ? 'search_off' : 'inventory_2' ?></span>
    </div>
    <p class="text-[15px] font-semibold text-on-surface"><?= $hasActiveFilters ? 'No items match your filters' : 'No inventory yet' ?></p>
    <p class="text-sm text-on-surface-variant mt-1 max-w-xs mx-auto leading-relaxed">
      <?= $hasActiveFilters ? 'Try a different search term or clear the filters.' : 'Add your first item to start tracking inventory across your warehouses.' ?>
    </p>
    <div class="mt-4 flex items-center justify-center gap-sm">
      <?php if ($hasActiveFilters): ?>
      <a href="<?= url('/inventory') ?>" class="btn-secondary inline-flex items-center gap-xs text-sm">
        <span class="material-symbols-outlined text-[15px]">filter_list_off</span> Clear filters
      </a>
      <?php endif; ?>
      <?php if (Auth::can('manage_inventory')): ?>
      <a href="<?= url('/inventory/create') ?>" class="<?= $hasActiveFilters ? 'btn-secondary' : 'btn-primary' ?> inline-flex items-center gap-xs text-sm">
        <span class="material-symbols-outlined text-[15px]">add</span> Add item
      </a>
      <?php endif; ?>
    </div>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-surface-container-low border-b border-outline-variant">
        <tr>
          <th class="text-left px-lg py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">SKU / Name</th>
          <th class="text-left px-md py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider hidden md:table-cell">Category</th>
          <th class="text-left px-md py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider hidden lg:table-cell">Warehouse</th>
          <th class="text-right px-md py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">Qty</th>
          <th class="text-right px-md py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider hidden md:table-cell">Value</th>
          <th class="text-center px-md py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">Status</th>
          <?php if (Auth::can('manage_inventory')): ?>
          <th class="text-center px-md py-sm text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">Actions</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody class="divide-y divide-outline-variant">
        <?php foreach ($pagination['data'] as $item): ?>
        <tr class="table-row">
          <td class="px-lg py-sm">
            <div>
              <p class="font-medium text-on-surface"><?= e($item['name']) ?></p>
              <p class="text-xs text-on-surface-variant font-mono"><?= e($item['sku']) ?></p>
            </div>
          </td>
          <td class="px-md py-sm hidden md:table-cell text-on-surface-variant"><?= e($item['category_name'] ?? '—') ?></td>
          <td class="px-md py-sm hidden lg:table-cell text-on-surface-variant"><?= e($item['warehouse_name'] ?? '—') ?></td>
          <td class="px-md py-sm text-right">
            <?php $lowStock = $item['quantity'] <= $item['reorder_point']; ?>
            <span class="font-semibold <?= $lowStock ? 'text-error' : 'text-on-surface' ?>">
              <?= number_format($item['quantity']) ?>
            </span>
            <span class="text-xs text-on-surface-variant"> <?= e($item['unit']) ?></span>
            <?php if ($lowStock): ?>
              <span class="material-symbols-outlined text-error text-[14px] ml-1">warning</span>
            <?php endif; ?>
          </td>
          <td class="px-md py-sm text-right hidden md:table-cell text-on-surface-variant">
            <?= money($item['quantity'] * $item['unit_price']) ?>
          </td>
          <td class="px-md py-sm text-center"><?= status_badge($item['status']) ?></td>
          <?php if (Auth::can('manage_inventory')): ?>
          <td class="px-md py-sm text-center">
            <div class="flex items-center justify-center gap-xs">
              <a href="<?= url('/inventory/'.$item['id'].'/edit') ?>"
                class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container rounded-lg transition-colors"
                title="Edit">
                <span class="material-symbols-outlined text-[16px]">edit</span>
              </a>
              <button onclick="confirmDelete(<?= $item['id'] ?>, '<?= e(addslashes($item['name'])) ?>')"
                class="p-1.5 text-on-surface-variant hover:text-error hover:bg-red-50 rounded-lg transition-colors"
                title="Delete">
                <span class="material-symbols-outlined text-[16px]">delete</span>
              </button>
            </div>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pagination['last_page'] > 1): ?>
  <div class="flex items-center justify-between px-lg py-sm border-t border-outline-variant">
    <p class="text-sm text-on-surface-variant">
      Showing <?= (($pagination['current_page']-1)*$pagination['per_page'])+1 ?>–<?= min($pagination['current_page']*$pagination['per_page'],$pagination['total']) ?> of <?= $pagination['total'] ?>
    </p>
    <?= paginate_links($pagination) ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Delete confirm modal -->
<div id="deleteModal" class="modal-backdrop hidden">
  <div class="modal animate-in">
    <div class="flex items-center gap-md mb-lg">
      <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
        <span class="material-symbols-outlined text-error">delete</span>
      </div>
      <h3 class="font-semibold text-lg">Delete item?</h3>
    </div>
    <p class="text-sm text-on-surface-variant mb-xl">
      Are you sure you want to delete <strong id="deleteItemName"></strong>? This cannot be undone.
    </p>
    <form id="deleteForm" method="POST">
      <?= csrf_field() ?>
      <div class="flex gap-md justify-end">
        <button type="button" onclick="closeModal('deleteModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="bg-error text-white px-lg py-sm rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">Delete</button>
      </div>
    </form>
  </div>
</div>

<script>
function confirmDelete(id, name) {
  document.getElementById('deleteItemName').textContent = name;
  document.getElementById('deleteForm').action = '<?= url('/inventory/') ?>' + id + '/delete';
  openModal('deleteModal');
}
</script>
