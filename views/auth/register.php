<h2 class="text-xl font-bold text-gray-900 mb-1">Create your workspace</h2>
<p class="text-sm text-[#434655] mb-6">Set up Wareflow for your company — it's free</p>

<form method="POST" action="<?= url('/register') ?>" novalidate>
  <?= csrf_field() ?>

  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Company name</label>
      <input type="text" name="company" value="<?= old('company') ?>" required
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="Acme Logistics"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Your name</label>
      <input type="text" name="name" value="<?= old('name') ?>" required
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="Alex Sterling"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Email address</label>
      <input type="email" name="email" value="<?= old('email') ?>" required
        class="w-full border border-[#c3c6d7] rounded-xl px-4 py-2.5 text-sm bg-[#f8f9ff] focus:border-[#004ac6]"
        placeholder="you@company.com"/>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
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
    Create workspace →
  </button>

  <p class="text-center text-xs text-gray-400 mt-4">
    By registering you agree to our Terms of Service.
  </p>
</form>

<p class="text-center text-sm text-[#434655] mt-4">
  Already have a workspace? <a href="<?= url('/login') ?>" class="text-[#004ac6] font-semibold hover:underline">Sign in</a>
</p>
