<?php $title = 'Users & Roles'; ?>
<div class="flex items-center justify-between mb-lg">
  <div>
    <h1 class="text-2xl font-bold text-on-surface">Users &amp; Roles</h1>
    <p class="text-sm text-on-surface-variant mt-0.5"><?= count($users) ?> member<?= count($users)!==1?'s':'' ?> in your workspace</p>
  </div>
  <?php if (Auth::can('manage_users')): ?>
  <button onclick="openModal('inviteModal')" class="btn-primary flex items-center gap-xs text-sm">
    <span class="material-symbols-outlined text-[18px]">person_add</span> Invite Member
  </button>
  <?php endif; ?>
</div>

<!-- Role legend -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-md mb-lg">
  <?php
  $roles = [
    ['owner',   'Owner',   'Full access, account management',               'bg-[#e1e0ff] text-[#3e3fcc] dark:bg-violet-900/30 dark:text-violet-300'],
    ['admin',   'Admin',   'Manage users, inventory, warehouses, settings', 'bg-[#eff4ff] text-primary dark:bg-blue-900/30 dark:text-blue-400'],
    ['manager', 'Manager', 'Manage inventory, warehouses, view reports',    'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'],
    ['viewer',  'Viewer',  'Read-only access to inventory and reports',     'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'],
  ];
  foreach ($roles as [$val, $label, $desc, $cls]): ?>
  <div class="card py-sm px-md">
    <span class="badge <?= $cls ?> mb-xs"><?= $label ?></span>
    <p class="text-xs text-on-surface-variant mt-1"><?= $desc ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- Users table -->
<div class="card p-0 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-surface-container-low border-b border-outline-variant">
      <tr>
        <th class="text-left px-lg py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Member</th>
        <th class="text-left px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide hidden md:table-cell">Email</th>
        <th class="text-center px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Role</th>
        <th class="text-center px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Status</th>
        <th class="text-left px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide hidden lg:table-cell">Joined</th>
        <?php if (Auth::can('manage_users')): ?>
        <th class="text-center px-md py-sm text-xs font-semibold text-on-surface-variant uppercase tracking-wide">Actions</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody class="divide-y divide-outline-variant">
      <?php
        $roleRanks = ['owner'=>4,'admin'=>3,'manager'=>2,'viewer'=>1];
        $myRank = $roleRanks[Auth::role()] ?? 0;
      ?>
      <?php foreach ($users as $u):
        $isMe = $u['id'] === Auth::id();
        $canManage = !$isMe && $myRank > ($roleRanks[$u['role']] ?? 0);
        $roleColors = ['owner'=>'bg-[#e1e0ff] text-[#3e3fcc] dark:bg-violet-900/30 dark:text-violet-300','admin'=>'bg-[#eff4ff] text-primary dark:bg-blue-900/30 dark:text-blue-400','manager'=>'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400','viewer'=>'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'];
        $rc = $roleColors[$u['role']] ?? 'bg-gray-100 text-gray-600';
      ?>
      <tr class="table-row <?= $u['status']==='suspended'?'opacity-60':'' ?>">
        <td class="px-lg py-sm">
          <div class="flex items-center gap-sm">
            <div class="w-8 h-8 bg-primary-container rounded-full flex items-center justify-center flex-shrink-0">
              <span class="text-xs font-bold text-on-primary"><?= strtoupper(substr($u['name'],0,1)) ?></span>
            </div>
            <div>
              <p class="font-medium"><?= e($u['name']) ?> <?= $isMe ? '<span class="text-xs text-on-surface-variant">(you)</span>' : '' ?></p>
              <p class="text-xs text-on-surface-variant md:hidden"><?= e($u['email']) ?></p>
            </div>
          </div>
        </td>
        <td class="px-md py-sm hidden md:table-cell text-on-surface-variant"><?= e($u['email']) ?></td>
        <td class="px-md py-sm text-center">
          <?php if (Auth::can('manage_users') && $canManage): ?>
          <form method="POST" action="<?= url('/users/'.$u['id'].'/role') ?>" class="inline">
            <?= csrf_field() ?>
            <select name="role" onchange="this.form.submit()"
              class="badge <?= $rc ?> border-0 cursor-pointer text-xs font-semibold bg-transparent">
              <?php foreach (['admin','manager','viewer'] as $r):
                if ($roleRanks[$r] >= $myRank) continue; ?>
              <option value="<?= $r ?>" <?= $u['role']===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
              <?php endforeach; ?>
            </select>
          </form>
          <?php else: ?>
          <span class="badge <?= $rc ?>"><?= ucfirst($u['role']) ?></span>
          <?php endif; ?>
        </td>
        <td class="px-md py-sm text-center"><?= status_badge($u['status']) ?></td>
        <td class="px-md py-sm hidden lg:table-cell text-on-surface-variant text-xs">
          <?= date('M j, Y', strtotime($u['created_at'])) ?>
          <?php if ($u['last_login']): ?>
          <br>Last: <?= ago($u['last_login']) ?>
          <?php endif; ?>
        </td>
        <?php if (Auth::can('manage_users')): ?>
        <td class="px-md py-sm text-center">
          <?php if ($canManage): ?>
          <div class="flex items-center justify-center gap-xs">
            <form method="POST" action="<?= url('/users/'.$u['id'].'/suspend') ?>">
              <?= csrf_field() ?>
              <button type="submit" class="p-1.5 text-on-surface-variant hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-colors"
                title="<?= $u['status']==='suspended' ? 'Activate' : 'Suspend' ?>">
                <span class="material-symbols-outlined text-[16px]"><?= $u['status']==='suspended' ? 'person' : 'person_off' ?></span>
              </button>
            </form>
            <form method="POST" action="<?= url('/users/'.$u['id'].'/delete') ?>"
              onsubmit="return confirm('Remove <?= e(addslashes($u['name'])) ?> from workspace?')">
              <?= csrf_field() ?>
              <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Remove">
                <span class="material-symbols-outlined text-[16px]">person_remove</span>
              </button>
            </form>
          </div>
          <?php else: ?>
          <span class="text-xs text-on-surface-variant">—</span>
          <?php endif; ?>
        </td>
        <?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Invite Modal -->
<?php if (Auth::can('manage_users')): ?>
<div id="inviteModal" class="modal-backdrop hidden">
  <div class="modal animate-in">
    <div class="flex items-center justify-between mb-lg">
      <h3 class="font-semibold text-lg">Invite Team Member</h3>
      <button onclick="closeModal('inviteModal')" class="text-on-surface-variant hover:text-on-surface">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <form method="POST" action="<?= url('/users/invite') ?>">
      <?= csrf_field() ?>
      <div class="space-y-md">
        <div>
          <label class="block text-sm font-medium mb-1.5">Email address <span class="text-error">*</span></label>
          <input type="email" name="email" required
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary"
            placeholder="colleague@company.com"/>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1.5">Role <span class="text-error">*</span></label>
          <select name="role" required class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <?php if ($myRank > 1): ?><option value="viewer">Viewer — Read-only access</option><?php endif; ?>
            <?php if ($myRank > 2): ?><option value="manager">Manager — Manage inventory &amp; warehouses</option><?php endif; ?>
            <?php if ($myRank > 3): ?><option value="admin">Admin — Full access except billing</option><?php endif; ?>
          </select>
        </div>
        <div class="bg-surface-container-low p-md rounded-lg text-sm text-on-surface-variant">
          <span class="material-symbols-outlined text-[14px] text-primary">info</span>
          An invite link will be generated. Share it with your colleague to complete signup.
        </div>
      </div>
      <div class="flex gap-md justify-end mt-xl">
        <button type="button" onclick="closeModal('inviteModal')" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Generate Invite Link</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>
