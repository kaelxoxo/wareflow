<?php $title = 'Subscription Required'; ?>
<div class="min-h-[60vh] flex items-center justify-center">
  <div class="text-center max-w-sm px-md">
    <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-lg dark:bg-amber-900/20">
      <span class="material-symbols-outlined text-amber-500" style="font-size:30px">credit_card_off</span>
    </div>
    <h1 class="text-xl font-bold text-on-surface mb-sm">Subscription Inactive</h1>
    <p class="text-sm text-on-surface-variant leading-relaxed">
      This workspace does not have an active Wareflow subscription.
      Please contact your workspace owner to renew access.
    </p>
    <a href="<?= url('/logout') ?>" class="inline-flex items-center gap-xs mt-lg text-sm text-on-surface-variant hover:text-error transition-colors">
      <span class="material-symbols-outlined" style="font-size:15px">logout</span>
      Sign out
    </a>
  </div>
</div>
