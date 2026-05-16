<h2 class="text-xl font-bold text-gray-900 mb-1">Accept your invitation</h2>
<p class="text-sm text-[#434655] mb-6">You've been invited as <strong><?= e($invitedUser['role']) ?></strong></p>

<form method="POST" action="<?= url('/invite/accept') ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= e($_GET['token'] ?? basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) ?>">

  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
      <input type="email" value="<?= e($invitedUser['email']) ?>" disabled
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-500"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Your name</label>
      <input type="text" name="name" required
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="Full name"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Create password</label>
      <input type="password" name="password" required minlength="8"
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="Min. 8 characters"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirm password</label>
      <input type="password" name="password_confirm" required
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="••••••••"/>
    </div>
  </div>

  <button type="submit" class="w-full mt-6 bg-[#004ac6] text-white font-semibold py-3 rounded-xl hover:bg-[#0053db] transition-all shadow text-sm">
    Join workspace →
  </button>
</form>
