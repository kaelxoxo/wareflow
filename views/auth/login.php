<?php $title = 'Sign In'; ?>

<h2 class="text-xl font-bold text-on-surface mb-1">Welcome back</h2>
<p class="text-sm text-on-surface-variant mb-6">Sign in to your workspace</p>

<form method="POST" action="<?= url('/login') ?>" novalidate>
  <?= csrf_field() ?>

  <div class="space-y-4">
    <div>
      <label for="login-email" class="block text-sm font-medium text-on-surface mb-1.5">
        Email address
      </label>
      <input type="email" id="login-email" name="email"
        value="<?= old('email') ?>" required autocomplete="email"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="you@company.com"/>
    </div>
    <div>
      <div class="flex items-center justify-between mb-1.5">
        <label for="login-password" class="text-sm font-medium text-on-surface">Password</label>
        <a href="<?= url('/forgot-password') ?>"
          class="text-xs text-primary font-medium hover:underline">
          Forgot password?
        </a>
      </div>
      <input type="password" id="login-password" name="password"
        required autocomplete="current-password"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="••••••••"/>
    </div>
  </div>

  <button type="submit"
    class="w-full mt-6 bg-primary text-on-primary font-semibold py-3 rounded-xl hover:bg-[#0053db] transition-all shadow text-sm">
    Sign in
  </button>
</form>

<p class="text-center text-sm text-on-surface-variant mt-6">
  Don't have a workspace?
  <a href="<?= url('/register') ?>" class="text-primary font-semibold hover:underline">Create one free</a>
</p>
