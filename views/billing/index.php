<?php
$title      = 'Billing';
$status     = $tenant['subscription_status'] ?? 'none';
$periodEnd  = $tenant['subscription_period_end'] ?? null;
$isActive   = in_array($status, ['active', 'trialing']);
$isPastDue  = $status === 'past_due';
$isCanceled = in_array($status, ['canceled', 'none', 'unpaid']);
$planKey    = $tenant['plan'] ?? 'starter';
$planConfig = PLANS[$planKey] ?? PLANS['starter'];
$planName   = 'Wareflow ' . $planConfig['name'];
?>

<div class="max-w-3xl mx-auto">

  <div class="mb-lg">
    <h1 class="text-2xl font-bold text-on-surface">Billing</h1>
    <p class="text-sm text-on-surface-variant mt-0.5">Manage your Wareflow subscription and plan.</p>
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
          <p class="font-semibold text-on-surface text-[15px]"><?= e($planName) ?></p>
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

    <?php if ($isActive && !empty($planConfig['features'])): ?>
    <div class="mt-md pt-md border-t border-outline-variant">
      <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide mb-sm">Included in your plan</p>
      <ul class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-md">
        <?php foreach ($planConfig['features'] as [$icon, $label]): ?>
        <li class="flex items-center gap-sm text-sm text-on-surface">
          <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:16px"><?= $icon ?></span>
          <?= e($label) ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

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
  <!-- Plan picker -->
  <div class="mb-md">
    <h2 class="text-[15px] font-semibold text-on-surface mb-xs">Choose a plan</h2>
    <p class="text-sm text-on-surface-variant">All plans billed monthly. Cancel anytime from the billing portal.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-md mb-md">

    <!-- Pro card -->
    <?php $pro = PLANS['pro']; ?>
    <div class="card flex flex-col border border-outline-variant">
      <div class="flex items-start justify-between mb-sm">
        <div>
          <p class="font-semibold text-on-surface text-[15px]">Pro</p>
          <p class="text-on-surface-variant text-xs mt-0.5">For growing businesses</p>
        </div>
        <div class="text-right">
          <p class="text-[26px] font-bold text-on-surface leading-none">$5</p>
          <p class="text-xs text-on-surface-variant mt-0.5">/ month</p>
        </div>
      </div>

      <ul class="space-y-2 mb-lg flex-1">
        <?php foreach ($pro['features'] as [$icon, $label]): ?>
        <li class="flex items-center gap-sm text-sm text-on-surface">
          <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:16px"><?= $icon ?></span>
          <?= e($label) ?>
        </li>
        <?php endforeach; ?>
      </ul>

      <form method="POST" action="<?= url('/billing/checkout') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="plan" value="pro">
        <button type="submit" class="w-full btn-secondary py-2.5 text-[13px] flex items-center justify-center gap-sm">
          <span class="material-symbols-outlined text-[15px]">credit_card</span>
          Subscribe — $5 / mo
        </button>
      </form>
    </div>

    <!-- Max card -->
    <?php $max = PLANS['max']; ?>
    <div class="card flex flex-col border-2 border-primary relative">
      <div class="absolute -top-3 left-1/2 -translate-x-1/2">
        <span class="bg-primary text-on-primary text-[11px] font-semibold px-3 py-0.5 rounded-full">Most popular</span>
      </div>

      <div class="flex items-start justify-between mb-sm mt-2">
        <div>
          <p class="font-semibold text-on-surface text-[15px]">Max</p>
          <p class="text-on-surface-variant text-xs mt-0.5">For larger operations</p>
        </div>
        <div class="text-right">
          <p class="text-[26px] font-bold text-on-surface leading-none">$10</p>
          <p class="text-xs text-on-surface-variant mt-0.5">/ month</p>
        </div>
      </div>

      <ul class="space-y-2 mb-lg flex-1">
        <?php foreach ($max['features'] as [$icon, $label]): ?>
        <li class="flex items-center gap-sm text-sm text-on-surface">
          <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:16px"><?= $icon ?></span>
          <?= e($label) ?>
        </li>
        <?php endforeach; ?>
      </ul>

      <form method="POST" action="<?= url('/billing/checkout') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="plan" value="max">
        <button type="submit" class="w-full btn-primary py-2.5 text-[13px] flex items-center justify-center gap-sm">
          <span class="material-symbols-outlined text-[15px]">credit_card</span>
          Subscribe — $10 / mo
        </button>
      </form>
    </div>

  </div>

  <p class="text-center text-xs text-on-surface-variant">
    Secured by Stripe &mdash; cancel anytime.
  </p>
  <?php endif; ?>

</div>
