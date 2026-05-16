<?php $title = 'Settings'; ?>
<div class="mb-xl">
  <h1 class="text-2xl font-bold text-on-surface">Settings</h1>
  <p class="text-sm text-on-surface-variant mt-0.5">Manage your workspace and profile</p>
</div>

<div class="grid lg:grid-cols-3 gap-lg">
  <!-- Sidebar nav -->
  <div class="space-y-xs">
    <?php
    $tabs = [
      ['workspace',  'Workspace',     'business'],
      ['profile',    'Profile',       'person'],
      ['password',   'Password',      'lock'],
      ['categories', 'Categories',    'label'],
    ];
    $activeTab = $_GET['tab'] ?? 'workspace';
    foreach ($tabs as [$val, $label, $icon]): ?>
    <a href="?tab=<?= $val ?>"
      class="flex items-center gap-sm p-md rounded-xl text-sm font-medium transition-colors
        <?= $activeTab===$val ? 'bg-primary text-on-primary shadow' : 'text-on-surface hover:bg-surface-container' ?>">
      <span class="material-symbols-outlined text-[18px]"><?= $icon ?></span>
      <?= $label ?>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Tab content -->
  <div class="lg:col-span-2">

    <?php if ($activeTab === 'workspace'): ?>
    <div class="card">
      <h2 class="font-semibold mb-lg">Workspace Settings</h2>
      <form method="POST" action="<?= url('/settings/tenant') ?>">
        <?= csrf_field() ?>
        <div class="space-y-md">
          <div>
            <label class="block text-sm font-medium mb-1.5">Company Name</label>
            <input type="text" name="company_name" value="<?= e($tenant['name']) ?>" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Workspace Slug</label>
            <input type="text" value="<?= e($tenant['slug']) ?>" disabled
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container text-on-surface-variant"/>
            <p class="text-xs text-on-surface-variant mt-1">Unique identifier — cannot be changed</p>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Plan</label>
            <div class="flex items-center gap-sm p-md rounded-lg bg-surface-container-low border border-outline-variant">
              <span class="material-symbols-outlined text-primary">workspace_premium</span>
              <div>
                <p class="font-semibold capitalize"><?= e($tenant['plan']) ?></p>
                <p class="text-xs text-on-surface-variant">Current subscription plan</p>
              </div>
            </div>
          </div>
        </div>
        <button type="submit" class="btn-primary mt-xl text-sm">Save Changes</button>
      </form>
    </div>

    <?php elseif ($activeTab === 'profile'): ?>
    <div class="card">
      <h2 class="font-semibold mb-lg">Profile</h2>
      <form method="POST" action="<?= url('/settings/profile') ?>">
        <?= csrf_field() ?>
        <div class="flex items-center gap-lg mb-lg">
          <div class="w-16 h-16 bg-primary-container rounded-full flex items-center justify-center text-2xl font-bold text-on-primary">
            <?= strtoupper(substr($user['name'],0,1)) ?>
          </div>
          <div>
            <p class="font-semibold"><?= e($user['name']) ?></p>
            <p class="text-sm text-on-surface-variant capitalize"><?= e($user['role']) ?></p>
          </div>
        </div>
        <div class="space-y-md">
          <div>
            <label class="block text-sm font-medium mb-1.5">Full Name</label>
            <input type="text" name="name" value="<?= e($user['name']) ?>" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Email Address</label>
            <input type="email" name="email" value="<?= e($user['email']) ?>" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"/>
          </div>
          <?php if ($user['last_login']): ?>
          <p class="text-xs text-on-surface-variant">Last login: <?= date('M j, Y H:i', strtotime($user['last_login'])) ?></p>
          <?php endif; ?>
        </div>
        <button type="submit" class="btn-primary mt-xl text-sm">Save Profile</button>
      </form>
    </div>

    <?php elseif ($activeTab === 'password'): ?>
    <div class="card">
      <h2 class="font-semibold mb-lg">Change Password</h2>
      <form method="POST" action="<?= url('/settings/password') ?>">
        <?= csrf_field() ?>
        <div class="space-y-md">
          <div>
            <label class="block text-sm font-medium mb-1.5">Current Password</label>
            <input type="password" name="current_password" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
              placeholder="••••••••"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">New Password</label>
            <input type="password" name="new_password" required minlength="8"
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
              placeholder="Min. 8 characters"/>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1.5">Confirm New Password</label>
            <input type="password" name="confirm_password" required
              class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
              placeholder="••••••••"/>
          </div>
        </div>
        <button type="submit" class="btn-primary mt-xl text-sm">Update Password</button>
      </form>
    </div>

    <?php elseif ($activeTab === 'categories'): ?>
    <div class="card">
      <div class="flex items-center justify-between mb-lg">
        <h2 class="font-semibold">Item Categories</h2>
        <button onclick="openModal('catModal')" class="btn-secondary text-sm flex items-center gap-xs">
          <span class="material-symbols-outlined text-[16px]">add</span> Add
        </button>
      </div>
      <?php if (empty($categories)): ?>
      <p class="text-sm text-on-surface-variant text-center py-8">No categories yet.</p>
      <?php else: ?>
      <div class="divide-y divide-outline-variant">
        <?php foreach ($categories as $cat): ?>
        <div class="flex items-center gap-sm py-sm">
          <div class="w-4 h-4 rounded-full flex-shrink-0" style="background:<?= e($cat['color']) ?>"></div>
          <p class="flex-1 font-medium"><?= e($cat['name']) ?></p>
          <span class="text-xs text-on-surface-variant"><?= $cat['item_count'] ?> items</span>
          <form method="POST" action="<?= url('/settings/categories/'.$cat['id'].'/delete') ?>"
            onsubmit="return confirm('Delete category \'<?= e(addslashes($cat['name'])) ?>\'?')">
            <?= csrf_field() ?>
            <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-red-50 rounded-lg transition-colors">
              <span class="material-symbols-outlined text-[16px]">delete</span>
            </button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Add Category Modal -->
    <div id="catModal" class="modal-backdrop hidden">
      <div class="modal animate-in">
        <div class="flex items-center justify-between mb-lg">
          <h3 class="font-semibold text-lg">Add Category</h3>
          <button onclick="closeModal('catModal')" class="text-on-surface-variant hover:text-on-surface">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
        <form method="POST" action="<?= url('/settings/categories') ?>">
          <?= csrf_field() ?>
          <div class="space-y-md">
            <div>
              <label class="block text-sm font-medium mb-1.5">Category Name <span class="text-error">*</span></label>
              <input type="text" name="cat_name" required
                class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
                placeholder="Electronics, Furniture…"/>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1.5">Color</label>
              <input type="color" name="cat_color" value="#004ac6"
                class="h-10 w-full border border-outline-variant rounded-lg cursor-pointer"/>
            </div>
          </div>
          <div class="flex gap-md justify-end mt-xl">
            <button type="button" onclick="closeModal('catModal')" class="btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Add Category</button>
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>
