<div class="grid sm:grid-cols-2 gap-md">
  <div>
    <label class="block text-sm font-medium mb-1.5">Name <span class="text-error">*</span></label>
    <input type="text" name="name" required
      class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
      placeholder="Main Warehouse"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1.5">Code</label>
    <input type="text" name="code"
      class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
      placeholder="WH-01"/>
  </div>
  <div class="sm:col-span-2">
    <label class="block text-sm font-medium mb-1.5">Location</label>
    <input type="text" name="location"
      class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
      placeholder="123 Industrial Ave, City"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1.5">Capacity (units)</label>
    <input type="number" name="capacity" value="0" min="0"
      class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
  </div>
  <div>
    <label class="block text-sm font-medium mb-1.5">Status</label>
    <select name="status" class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
      <option value="active">Active</option>
      <option value="inactive">Inactive</option>
    </select>
  </div>
  <div class="sm:col-span-2">
    <label class="block text-sm font-medium mb-1.5">Manager</label>
    <select name="manager_id" class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
      <option value="">— No manager —</option>
      <?php foreach ($users as $u): ?>
      <option value="<?= $u['id'] ?>"><?= e($u['name']) ?> (<?= e($u['role']) ?>)</option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
