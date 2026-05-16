<h2 class="text-xl font-bold text-gray-900 mb-1">Welcome back</h2>
<p class="text-sm text-[#434655] mb-6">Sign in to your workspace</p>

<form method="POST" action="<?= url('/login') ?>" novalidate>
  <?= csrf_field() ?>

  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
      <input type="email" name="email" value="<?= old('email') ?>" required autocomplete="email"
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="you@company.com"/>
    </div>
    <div>
      <div class="flex justify-between mb-1.5">
        <label class="text-sm font-medium text-gray-700">Password</label>
      </div>
      <input type="password" name="password" required autocomplete="current-password"
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="••••••••"/>
    </div>
  </div>

  <button type="submit" class="w-full mt-6 bg-[#004ac6] text-white font-semibold py-3 rounded-xl hover:bg-[#0053db] transition-all shadow text-sm">
    Sign in
  </button>
</form>

<p class="text-center text-sm text-[#434655] mt-6">
  Don't have a workspace?
  <a href="<?= url('/register') ?>" class="text-[#004ac6] font-semibold hover:underline">Create one free</a>
</p>
