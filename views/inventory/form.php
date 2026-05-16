<?php
$isEdit = isset($item);
$title  = $isEdit ? 'Edit Item' : 'Add Item';
?>
<div class="flex items-center gap-md mb-xl">
  <a href="<?= url('/inventory') ?>" class="text-on-surface-variant hover:text-on-surface transition-colors">
    <span class="material-symbols-outlined">arrow_back</span>
  </a>
  <div>
    <h1 class="text-2xl font-bold text-on-surface"><?= $title ?></h1>
    <p class="text-sm text-on-surface-variant"><?= $isEdit ? 'SKU: '.e($item['sku']) : 'Add a new inventory item' ?></p>
  </div>
</div>

<form method="POST" action="<?= $isEdit ? url('/inventory/'.$item['id'].'/update') : url('/inventory') ?>" novalidate>
  <?= csrf_field() ?>
  <div class="grid lg:grid-cols-3 gap-lg">

    <!-- Main info -->
    <div class="lg:col-span-2 space-y-lg">
      <div class="card">
        <h2 class="font-semibold mb-lg">Item Details</h2>
        <div class="grid sm:grid-cols-2 gap-md">
          <div>
            <label class="block text-sm font-medium mb-1.5">SKU <span class="text-error">*</span></label>
            <input type="text" name="sku" value="<?= old('sku', $item['sku'] ?? '') ?>" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
              placeholder="e.g. PROD-001"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Name <span class="text-error">*</span></label>
            <input type="text" name="name" value="<?= old('name', $item['name'] ?? '') ?>" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
              placeholder="Product name"/>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium mb-1.5">Description</label>
            <textarea name="description" rows="3"
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary resize-none"
              placeholder="Optional description"><?= old('description', $item['description'] ?? '') ?></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Category</label>
            <select name="category_id" class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
              <option value="">— No category —</option>
              <?php foreach ($categories as $c): ?>
              <option value="<?= $c['id'] ?>" <?= old('category_id',$item['category_id']??'')==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Warehouse</label>
            <select name="warehouse_id" class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
              <option value="">— No warehouse —</option>
              <?php foreach ($warehouses as $w): ?>
              <option value="<?= $w['id'] ?>" <?= old('warehouse_id',$item['warehouse_id']??'')==$w['id']?'selected':'' ?>><?= e($w['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- Quantity & Pricing -->
      <div class="card">
        <h2 class="font-semibold mb-lg">Quantity &amp; Pricing</h2>
        <div class="grid sm:grid-cols-4 gap-md">
          <div>
            <label class="block text-sm font-medium mb-1.5">Quantity</label>
            <input type="number" name="quantity" value="<?= old('quantity', $item['quantity'] ?? 0) ?>" min="0"
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Unit</label>
            <input type="text" name="unit" value="<?= old('unit', $item['unit'] ?? 'pcs') ?>"
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
              placeholder="pcs, kg, m…"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Unit Price ($)</label>
            <input type="number" name="unit_price" value="<?= old('unit_price', $item['unit_price'] ?? 0) ?>" min="0" step="0.01"
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Reorder Point</label>
            <input type="number" name="reorder_point" value="<?= old('reorder_point', $item['reorder_point'] ?? 10) ?>" min="0"
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
          </div>
        </div>
      </div>

      <!-- Custom fields -->
      <?php if (!empty($customFields)): ?>
      <div class="card">
        <h2 class="font-semibold mb-lg">Custom Fields</h2>
        <div class="grid sm:grid-cols-2 gap-md">
          <?php foreach ($customFields as $cf):
            $val = $isEdit ? ($cf['value'] ?? '') : old('custom_fields.'.$cf['id'],'');
          ?>
          <div <?= in_array($cf['field_type'],['textarea']) ? 'class="sm:col-span-2"' : '' ?>>
            <label class="block text-sm font-medium mb-1.5">
              <?= e($cf['label']) ?>
              <?php if ($cf['required']): ?><span class="text-error">*</span><?php endif; ?>
            </label>
            <?php if ($cf['field_type'] === 'textarea'): ?>
              <textarea name="custom_fields[<?= $cf['id'] ?>]" rows="2" <?= $cf['required']?'required':'' ?>
                class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary resize-none"
                ><?= e($val) ?></textarea>
            <?php elseif ($cf['field_type'] === 'select'): ?>
              <select name="custom_fields[<?= $cf['id'] ?>]" <?= $cf['required']?'required':'' ?>
                class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
                <option value="">— Select —</option>
                <?php foreach ($cf['options'] ?? [] as $opt): ?>
                <option value="<?= e($opt) ?>" <?= $val===$opt?'selected':'' ?>><?= e($opt) ?></option>
                <?php endforeach; ?>
              </select>
            <?php elseif ($cf['field_type'] === 'checkbox'): ?>
              <label class="flex items-center gap-sm cursor-pointer">
                <input type="checkbox" name="custom_fields[<?= $cf['id'] ?>]" value="1" <?= $val?'checked':'' ?>
                  class="rounded border-outline-variant text-primary w-4 h-4">
                <span class="text-sm"><?= e($cf['label']) ?></span>
              </label>
            <?php else: ?>
              <input type="<?= $cf['field_type'] ?>" name="custom_fields[<?= $cf['id'] ?>]"
                value="<?= e($val) ?>" <?= $cf['required']?'required':'' ?>
                class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar: Status -->
    <div class="space-y-lg">
      <div class="card">
        <h2 class="font-semibold mb-lg">Status</h2>
        <div class="space-y-2">
          <?php foreach (['active'=>'Active','inactive'=>'Inactive','discontinued'=>'Discontinued'] as $val=>$label): ?>
          <label class="flex items-center gap-sm cursor-pointer p-sm rounded-lg hover:bg-surface-container-low transition-colors">
            <input type="radio" name="status" value="<?= $val ?>"
              <?= old('status',$item['status']??'active')===$val?'checked':'' ?>
              class="text-primary border-outline-variant">
            <span class="text-sm font-medium"><?= $label ?></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card">
        <div class="space-y-md">
          <button type="submit" class="w-full btn-primary text-sm py-3">
            <?= $isEdit ? 'Save Changes' : 'Add Item' ?>
          </button>
          <a href="<?= url('/inventory') ?>" class="w-full btn-secondary text-sm py-2.5 text-center block">
            Cancel
          </a>
        </div>
      </div>
    </div>

  </div>
</form>
