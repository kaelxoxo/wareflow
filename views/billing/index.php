<?php
$title  = 'Billing';
$status = $tenant['subscription_status'] ?? 'none';
$periodEnd = $tenant['subscription_period_end'] ?? null;
$isActive  = in_array($status, ['active', 'trialing']);
$isPastDue = $status === 'past_due';
$isCanceled = in_array($status, ['canceled', 'none', 'unpaid']);
?>

<div class="max-w-2xl mx-auto">

  <div class="mb-lg">
    <h1 class="text-2xl font-bold text-on-surface">Billing</h1>
    <p class="text-sm text-on-surface-variant mt-0.5">Manage your Wareflow Pro subscription.</p>
  </div>

  <!-- Status card -->
  <div class="card mb-md">
    <div class="flex items-start justify-between gap-md flex-wrap">
      <div class="flex items-center gap-md">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
          <?= $isActive ? 'bg-emerald-50' : ($isPastDue ? 'bg-amber-50' : 'bg-surface-container-low') ?>">
          <span class="material-symbols-outlined <?= $isActive ? 'text-emerald-600' : ($isPastDue ? 'text-amber-600' : 'text-on-surface-variant') ?>" style="font-size:21px">
            <?= $isActive ? 'verified' : ($isPastDue ? 'warning' : 'credit_card_off') ?>
          </span>
        </div>
        <div>
          <p class="font-semibold text-on-surface text-[15px]">Wareflow Pro</p>
          <p class="text-sm text-on-surface-variant mt-0.5">
            <?php if ($isActive): ?>
              <?= $status === 'trialing' ? 'Free trial active' : 'Monthly subscription' ?> &mdash;
              <?= $periodEnd ? 'renews ' . date('M j, Y', strtotime($periodEnd)) : 'active' ?>
            <?php elseif ($isPastDue): ?>
              Payment failed &mdash; update your payment method to keep access.
            <?php else: ?>
              No active subscription.
            <?php endif; ?>
          </p>
        </div>
      </div>
      <div class="flex-shrink-0">
        <?php if ($isActive || $isPastDue): ?>
          <?php if (!empty($tenant['stripe_customer_id'])): ?>
          <form method="POST" action="<?= url('/billing/portal') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn-secondary text-sm flex items-center gap-xs">
              <span class="material-symbols-outlined text-[15px]">open_in_new</span>
              <?= $isPastDue ? 'Update Payment' : 'Manage Subscription' ?>
            </button>
          </form>
          <?php endif; ?>
        <?php else: ?>
          <span class="badge bg-surface-container-low text-on-surface-variant">Inactive</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($isPastDue): ?>
    <div class="mt-md px-md py-3 bg-amber-50 border border-amber-200 rounded-xl dark:bg-amber-900/10 dark:border-amber-700/30 flex items-start gap-sm">
      <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 flex-shrink-0" style="font-size:16px;margin-top:1px">info</span>
      <p class="text-sm text-amber-800 dark:text-amber-300">
        Your last payment did not go through. Access may be restricted soon. Open the billing portal to update your card.
      </p>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($isCanceled): ?>
  <!-- Subscribe card -->
  <div class="card border-primary/20">
    <div class="flex items-center justify-between mb-lg flex-wrap gap-sm">
      <div>
        <p class="font-semibold text-on-surface text-[15px]">Wareflow Pro &mdash; Monthly</p>
        <p class="text-on-surface-variant text-sm mt-0.5">Everything you need to run your warehouse.</p>
      </div>
      <div class="text-right">
        <p class="text-[28px] font-bold text-on-surface leading-none">$5</p>
        <p class="text-xs text-on-surface-variant mt-0.5">per month</p>
      </div>
    </div>

    <ul class="space-y-2.5 mb-lg">
      <?php
      $features = [
        ['inventory_2', 'Unlimited inventory items and SKUs'],
        ['warehouse',   'Multi-warehouse support'],
        ['swap_horiz',  'Full stock movement history'],
        ['group',       'Team members with role-based access'],
        ['tune',        'Custom fields on any entity'],
        ['bar_chart',   'KPI dashboard and trend charts'],
      ];
      foreach ($features as [$icon, $label]):
      ?>
      <li class="flex items-center gap-sm text-sm text-on-surface">
        <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:17px"><?= $icon ?></span>
        <?= e($label) ?>
      </li>
      <?php endforeach; ?>
    </ul>

    <form method="POST" action="<?= url('/billing/checkout') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="w-full btn-primary py-3 text-[14px] flex items-center justify-center gap-sm">
        <span class="material-symbols-outlined text-[17px]">credit_card</span>
        Subscribe for $5 / month
      </button>
    </form>

    <p class="text-center text-xs text-on-surface-variant mt-md">
      Secured by Stripe. Cancel anytime from the billing portal.
    </p>
  </div>
  <?php endif; ?>

</div>
