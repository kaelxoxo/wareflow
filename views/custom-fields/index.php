<?php $title = 'Custom Fields'; ?>
<div class="flex items-center justify-between mb-lg">
  <div>
    <h1 class="text-2xl font-bold text-on-surface">Custom Fields Builder</h1>
    <p class="text-sm text-on-surface-variant mt-0.5">Extend inventory items with custom attributes — no code required</p>
  </div>
  <button onclick="openModal('createModal')" class="btn-primary flex items-center gap-xs text-sm">
    <span class="material-symbols-outlined text-[18px]">add</span> Add Field
  </button>
</div>

<div class="grid lg:grid-cols-3 gap-lg">
  <!-- Field list -->
  <div class="lg:col-span-2">
    <?php if (empty($fields)): ?>
    <div class="card text-center py-16">
      <span class="material-symbols-outlined text-5xl text-on-surface-variant">tune</span>
      <h3 class="font-semibold mt-3">No custom fields yet</h3>
      <p class="text-sm text-on-surface-variant mt-1 mb-6">Add fields to capture data specific to your business.</p>
      <button onclick="openModal('createModal')" class="btn-primary inline-flex items-center gap-xs text-sm">
        <span class="material-symbols-outlined text-[16px]">add</span> Create first field
      </button>
    </div>
    <?php else: ?>
    <div class="card p-0 overflow-hidden">
      <div class="px-lg py-sm border-b border-outline-variant bg-surface-container-low">
        <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide"><?= count($fields) ?> custom field<?= count($fields)!==1?'s':'' ?> for inventory items</p>
      </div>
      <div class="divide-y divide-outline-variant">
        <?php
        $typeIcons = ['text'=>'text_fields','number'=>'tag','date'=>'calendar_today','select'=>'arrow_drop_down_circle',
                      'checkbox'=>'check_box','textarea'=>'notes','url'=>'link','email'=>'email'];
        $typeLabels = ['text'=>'Text','number'=>'Number','date'=>'Date','select'=>'Select (dropdown)',
                       'checkbox'=>'Checkbox','textarea'=>'Long Text','url'=>'URL','email'=>'Email'];
        foreach ($fields as $field):
          $icon = $typeIcons[$field['field_type']] ?? 'text_fields';
        ?>
        <div class="flex items-center gap-md p-lg">
          <div class="w-10 h-10 bg-surface-container rounded-xl flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-primary text-[18px]"><?= $icon ?></span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-sm">
              <p class="font-medium text-on-surface"><?= e($field['label']) ?></p>
              <?php if ($field['required']): ?>
              <span class="badge bg-[#eff4ff] text-primary text-[10px]">Required</span>
              <?php endif; ?>
            </div>
            <div class="flex items-center gap-md mt-0.5">
              <span class="text-xs text-on-surface-variant"><?= $typeLabels[$field['field_type']] ?? $field['field_type'] ?></span>
              <span class="text-xs text-on-surface-variant font-mono">key: <?= e($field['field_key']) ?></span>
              <?php if ($field['field_type'] === 'select' && !empty($field['options'])): ?>
              <span class="text-xs text-on-surface-variant">
                <?= count(json_decode($field['options'], true) ?? []) ?> options
              </span>
              <?php endif; ?>
            </div>
          </div>
          <form method="POST" action="<?= url('/custom-fields/'.$field['id'].'/delete') ?>"
            onsubmit="return confirm('Delete field \'<?= e(addslashes($field['label'])) ?>\'? All values will be lost.')">
            <?= csrf_field() ?>
            <button type="submit" class="p-2 text-on-surface-variant hover:text-error hover:bg-red-50 rounded-lg transition-colors">
              <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Info sidebar -->
  <div class="space-y-lg">
    <div class="card">
      <h3 class="font-semibold mb-md flex items-center gap-sm">
        <span class="material-symbols-outlined text-primary text-[18px]">info</span> Field Types
      </h3>
      <div class="space-y-2 text-sm">
        <?php foreach ([
          ['Text',        'Short text input for names, codes'],
          ['Number',      'Numeric values with decimal support'],
          ['Date',        'Date picker'],
          ['Select',      'Dropdown with predefined options'],
          ['Checkbox',    'Boolean yes/no toggle'],
          ['Long Text',   'Multi-line text area'],
          ['URL',         'Web address with link validation'],
          ['Email',       'Email address with validation'],
        ] as [$name, $desc]): ?>
        <div class="flex gap-sm">
          <span class="font-medium text-on-surface w-20 flex-shrink-0"><?= $name ?></span>
          <span class="text-on-surface-variant"><?= $desc ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="card bg-[#eff4ff] border-primary/20">
      <h3 class="font-semibold mb-sm text-primary">Pro tip</h3>
      <p class="text-sm text-on-surface-variant">Custom fields appear in the Add/Edit Item form and can be used to filter and organize your inventory.</p>
    </div>
  </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal-backdrop hidden">
  <div class="modal animate-in">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-lg">Add Custom Field</h3>
      <button onclick="closeModal('createModal')" class="text-on-surface-variant hover:text-on-surface">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="<?= url('/custom-fields') ?>">
      <?= csrf_field() ?>
      <div class="space-y-md">
        <div>
          <label class="block text-sm font-medium mb-1.5">Field Label <span class="text-error">*</span></label>
          <input type="text" name="label" required
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
            placeholder="e.g. Serial Number, Supplier, Color"/>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Field Type <span class="text-error">*</span></label>
          <select name="field_type" id="fieldType" onchange="toggleOptions(this.value)" required
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <option value="">— Select type —</option>
            <option value="text">Text</option>
            <option value="number">Number</option>
            <option value="date">Date</option>
            <option value="select">Select (Dropdown)</option>
            <option value="checkbox">Checkbox</option>
            <option value="textarea">Long Text</option>
            <option value="url">URL</option>
            <option value="email">Email</option>
          </select>
        </div>
        <div id="optionsField" class="hidden">
          <label class="block text-sm font-medium mb-1.5">Options <span class="text-xs font-normal text-on-surface-variant">(one per line)</span></label>
          <textarea name="options" rows="4"
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary resize-none"
            placeholder="Red&#10;Green&#10;Blue&#10;Yellow"></textarea>
        </div>
        <label class="flex items-center gap-sm cursor-pointer">
          <input type="checkbox" name="required" value="1" class="rounded border-outline-variant text-primary w-4 h-4">
          <span class="text-sm font-medium">Required field</span>
        </label>
      </div>
      <div class="flex gap-md justify-end mt-xl">
        <button type="button" onclick="closeModal('createModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Add Field</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleOptions(type) {
  document.getElementById('optionsField').classList.toggle('hidden', type !== 'select');
}
</script>
