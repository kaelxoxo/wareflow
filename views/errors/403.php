<?php $title = '403 Forbidden'; ?>
<div class="flex flex-col items-center justify-center py-32 text-center">
  <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-lg">
    <span class="material-symbols-outlined text-error text-3xl">lock</span>
  </div>
  <h1 class="text-3xl font-bold text-on-surface mb-sm">Access Denied</h1>
  <p class="text-on-surface-variant mb-xl max-w-sm">You don't have permission to view this page. Contact your workspace owner if you think this is a mistake.</p>
  <a href="<?= url('/dashboard') ?>" class="btn-primary">← Back to Dashboard</a>
</div>
