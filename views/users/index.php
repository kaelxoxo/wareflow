<?php $title = 'Users & Roles'; ?>

<script>
/* Role inline edit */
function editRole(id) {
  document.getElementById('role-view-' + id).classList.add('hidden');
  var el = document.getElementById('role-edit-' + id);
  el.classList.remove('hidden');
  el.classList.add('flex');
}
function cancelRole(id) {
  var el = document.getElementById('role-edit-' + id);
  el.classList.remove('flex');
  el.classList.add('hidden');
  document.getElementById('role-view-' + id).classList.remove('hidden');
}

/* Delete inline confirm */
function showRemove(id) {
  document.getElementById('actions-' + id).classList.add('hidden');
  document.getElementById('remove-confirm-' + id).classList.remove('hidden');
}
function cancelRemove(id) {
  document.getElementById('remove-confirm-' + id).classList.add('hidden');
  document.getElementById('actions-' + id).classList.remove('hidden');
}
</script>

<div class="flex items-center justify-between mb-lg">
  <div>
    <h1 class="text-2xl font-bold text-on-surface">Users &amp; Roles</h1>
    <p class="text-sm text-on-surface-variant mt-0.5"><?= count($users) ?> member<?= count($users) !== 1 ? 's' : '' ?> in your workspace</p>
  </div>
  <?php if (Auth::can('manage_users')): ?>
  <button onclick="openModal('inviteModal')" class="btn-primary flex items-center gap-xs text-sm">
    <span class="material-symbols-outlined text-[18px]">person_add</span> Invite Member
  </button>
  <?php endif; ?>
</div>

<!-- Users table -->
<div class="card p-0 overflow-hidden">
  <table class="w-full text-sm" aria-label="Workspace members">
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
        $roleRanks  = ['owner' => 4, 'admin' => 3, 'manager' => 2, 'viewer' => 1];
        $myRank     = $roleRanks[Auth::role()] ?? 0;
        $avatarCols = [
          'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
          'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
          'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
          'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
          'bg-rose-100 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400',
          'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/20 dark:text-cyan-400',
        ];
        $roleColors = [
          'owner'   => 'bg-[#e1e0ff] text-[#3e3fcc] dark:bg-violet-900/30 dark:text-violet-300',
          'admin'   => 'bg-[#eff4ff] text-primary dark:bg-blue-900/30 dark:text-blue-400',
          'manager' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
          'viewer'  => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
        ];
      ?>
      <?php foreach ($users as $u):
        $isMe        = $u['id'] === Auth::id();
        $canManage   = !$isMe && $myRank > ($roleRanks[$u['role']] ?? 0);
        $rc          = $roleColors[$u['role']] ?? 'bg-gray-100 text-gray-600';
        $avatarCls   = $avatarCols[ord(strtolower($u['name'][0] ?? 'a')) % 6];
        $isSuspended = $u['status'] === 'suspended';
      ?>
      <tr class="table-row <?= $isSuspended ? 'opacity-60' : '' ?>">

        <!-- Member -->
        <td class="px-lg py-sm">
          <div class="flex items-center gap-sm">
            <div class="w-8 h-8 <?= $avatarCls ?> rounded-full flex items-center justify-center flex-shrink-0" aria-hidden="true">
              <span class="text-xs font-bold"><?= strtoupper(substr($u['name'], 0, 1)) ?></span>
            </div>
            <div>
              <p class="font-medium text-on-surface">
                <?= e($u['name']) ?>
                <?= $isMe ? '<span class="text-xs text-on-surface-variant font-normal">(you)</span>' : '' ?>
              </p>
              <p class="text-xs text-on-surface-variant md:hidden"><?= e($u['email']) ?></p>
            </div>
          </div>
        </td>

        <!-- Email -->
        <td class="px-md py-sm hidden md:table-cell text-on-surface-variant"><?= e($u['email']) ?></td>

        <!-- Role -->
        <td class="px-md py-sm text-center">
          <?php if (Auth::can('manage_users') && $canManage): ?>
          <div id="role-view-<?= $u['id'] ?>" class="flex items-center justify-center gap-1">
            <span class="badge <?= $rc ?>"><?= ucfirst($u['role']) ?></span>
            <button type="button" onclick="editRole(<?= $u['id'] ?>)"
              class="p-1 rounded text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors"
              title="Change role">
              <span class="material-symbols-outlined" style="font-size:13px">edit</span>
            </button>
          </div>
          <form id="role-edit-<?= $u['id'] ?>" method="POST" action="<?= url('/users/'.$u['id'].'/role') ?>"
            class="hidden items-center justify-center gap-1">
            <?= csrf_field() ?>
            <select name="role"
              class="border border-outline-variant rounded-lg px-2 py-1 text-xs bg-surface-container-low focus:border-primary text-on-surface">
              <?php foreach (['admin', 'manager', 'viewer'] as $r):
                if ($roleRanks[$r] >= $myRank) continue; ?>
              <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit"
              class="p-1 rounded text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors"
              title="Save">
              <span class="material-symbols-outlined" style="font-size:15px">check</span>
            </button>
            <button type="button" onclick="cancelRole(<?= $u['id'] ?>)"
              class="p-1 rounded text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors"
              title="Cancel">
              <span class="material-symbols-outlined" style="font-size:15px">close</span>
            </button>
          </form>
          <?php else: ?>
          <span class="badge <?= $rc ?>"><?= ucfirst($u['role']) ?></span>
          <?php endif; ?>
        </td>

        <!-- Status -->
        <td class="px-md py-sm text-center"><?= status_badge($u['status']) ?></td>

        <!-- Joined -->
        <td class="px-md py-sm hidden lg:table-cell text-on-surface-variant text-xs">
          <div class="flex flex-col gap-0.5">
            <span><?= date('M j, Y', strtotime($u['created_at'])) ?></span>
            <?php if ($u['last_login']): ?>
            <span>Last login: <?= ago($u['last_login']) ?></span>
            <?php endif; ?>
          </div>
        </td>

        <!-- Actions -->
        <?php if (Auth::can('manage_users')): ?>
        <td class="px-md py-sm text-center">
          <?php if ($canManage): ?>
          <div id="actions-<?= $u['id'] ?>" class="flex items-center justify-center gap-xs">
            <form method="POST" action="<?= url('/users/'.$u['id'].'/suspend') ?>">
              <?= csrf_field() ?>
              <button type="submit"
                class="p-1.5 rounded-lg transition-colors <?= $isSuspended
                  ? 'text-emerald-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'
                  : 'text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20' ?>"
                title="<?= $isSuspended ? 'Reactivate' : 'Suspend' ?>">
                <span class="material-symbols-outlined text-[16px]"><?= $isSuspended ? 'person' : 'person_off' ?></span>
              </button>
            </form>
            <button type="button" onclick="showRemove(<?= $u['id'] ?>)"
              class="p-1.5 text-on-surface-variant hover:text-error hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
              title="Remove">
              <span class="material-symbols-outlined text-[16px]">person_remove</span>
            </button>
          </div>
          <div id="remove-confirm-<?= $u['id'] ?>" class="hidden">
            <div class="flex items-center justify-center gap-1.5 flex-wrap">
              <span class="text-[11px] text-on-surface-variant whitespace-nowrap">Remove?</span>
              <form method="POST" action="<?= url('/users/'.$u['id'].'/delete') ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit"
                  class="px-2.5 py-1 text-[11px] font-semibold bg-error text-white rounded-lg hover:opacity-90 transition-opacity">
                  Remove
                </button>
              </form>
              <button type="button" onclick="cancelRemove(<?= $u['id'] ?>)"
                class="px-2.5 py-1 text-[11px] font-semibold text-on-surface-variant hover:text-on-surface transition-colors">
                Cancel
              </button>
            </div>
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
      <button onclick="closeModal('inviteModal')" class="text-on-surface-variant hover:text-on-surface transition-colors">
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
          <select name="role" required
            class="w-full border border-outline-variant rounded-lg px-4 py-2.5 text-sm bg-surface-container-low focus:border-primary">
            <?php if ($myRank > 1): ?><option value="viewer">Viewer — Read-only access</option><?php endif; ?>
            <?php if ($myRank > 2): ?><option value="manager">Manager — Manage inventory &amp; warehouses</option><?php endif; ?>
            <?php if ($myRank > 3): ?><option value="admin">Admin — Full access except billing</option><?php endif; ?>
          </select>
        </div>

        <!-- Role reference -->
        <div class="border border-outline-variant rounded-xl overflow-hidden divide-y divide-outline-variant">
          <?php foreach ([
            ['Owner',   'Full access, account management',               'bg-[#e1e0ff] text-[#3e3fcc] dark:bg-violet-900/30 dark:text-violet-300'],
            ['Admin',   'Manage users, inventory, warehouses, settings', 'bg-[#eff4ff] text-primary dark:bg-blue-900/30 dark:text-blue-400'],
            ['Manager', 'Manage inventory, warehouses, view reports',    'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400'],
            ['Viewer',  'Read-only access to inventory and reports',     'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'],
          ] as [$lbl, $desc, $cls]): ?>
          <div class="flex items-center gap-sm px-sm py-[7px] bg-surface-container-low/50">
            <span class="badge <?= $cls ?> flex-shrink-0 text-[10px]"><?= $lbl ?></span>
            <span class="text-xs text-on-surface-variant"><?= $desc ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="flex items-start gap-xs bg-surface-container-low p-md rounded-lg text-sm text-on-surface-variant">
          <span class="material-symbols-outlined text-[14px] text-primary flex-shrink-0 mt-0.5">info</span>
          <span>Your colleague will receive a link to create their account.</span>
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
