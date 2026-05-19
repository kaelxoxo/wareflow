<?php
$iconColor = match($reason ?? 'invalid') {
    'accepted' => 'text-emerald-500',
    default    => 'text-error',
};
$bgColor = match($reason ?? 'invalid') {
    'accepted' => 'bg-emerald-50',
    default    => 'bg-red-50',
};
?>
<div class="text-center max-w-sm w-full mx-auto">
  <div class="w-16 h-16 <?= $bgColor ?> rounded-2xl flex items-center justify-center mx-auto mb-6">
    <span class="material-symbols-outlined <?= $iconColor ?>" style="font-size:30px"><?= e($error['icon']) ?></span>
  </div>
  <h1 class="text-xl font-bold text-gray-900 mb-2"><?= e($error['title']) ?></h1>
  <p class="text-sm text-[#434655] leading-relaxed mb-8"><?= e($error['msg']) ?></p>
  <a href="<?= url('/login') ?>"
    class="inline-flex items-center gap-2 bg-[#004ac6] text-white font-semibold py-2.5 px-6 rounded-xl hover:bg-[#0053db] transition-all text-sm">
    <span class="material-symbols-outlined" style="font-size:16px">login</span>
    Go to Sign In
  </a>
</div>
