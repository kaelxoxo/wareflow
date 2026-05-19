<?php $title = 'Create your workspace'; ?>

<h2 class="text-xl font-bold text-on-surface mb-1">Create your workspace</h2>
<p class="text-sm text-on-surface-variant mb-6">Set up Wareflow for your company — it's free</p>

<form method="POST" action="<?= url('/register') ?>" novalidate>
  <?= csrf_field() ?>

  <div class="space-y-4">
    <div>
      <label for="reg-company" class="block text-sm font-medium text-on-surface mb-1.5">
        Company name
      </label>
      <input type="text" id="reg-company" name="company"
        value="<?= old('company') ?>" required autocomplete="organization"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="Acme Logistics"/>
    </div>
    <div>
      <label for="reg-name" class="block text-sm font-medium text-on-surface mb-1.5">
        Your name
      </label>
      <input type="text" id="reg-name" name="name"
        value="<?= old('name') ?>" required autocomplete="name"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="Alex Sterling"/>
    </div>
    <div>
      <label for="reg-email" class="block text-sm font-medium text-on-surface mb-1.5">
        Email address
      </label>
      <input type="email" id="reg-email" name="email"
        value="<?= old('email') ?>" required autocomplete="email"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="you@company.com"/>
    </div>
    <div>
      <label for="reg-password" class="block text-sm font-medium text-on-surface mb-1.5">
        Password
      </label>
      <input type="password" id="reg-password" name="password"
        required minlength="8" autocomplete="new-password"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="••••••••"
        oninput="checkPasswordMatch()"/>
      <p class="text-xs text-on-surface-variant mt-1.5">At least 8 characters</p>
    </div>
    <div>
      <label for="reg-confirm" class="block text-sm font-medium text-on-surface mb-1.5">
        Confirm password
      </label>
      <input type="password" id="reg-confirm" name="password_confirm"
        required autocomplete="new-password"
        class="w-full border border-outline-variant rounded-xl px-4 py-2.5 text-sm bg-surface-container-low text-on-surface focus:border-primary"
        placeholder="••••••••"
        oninput="checkPasswordMatch()"/>
      <p id="pw-match-msg" class="text-xs mt-1.5 hidden" aria-live="polite"></p>
    </div>
  </div>

  <button type="submit"
    class="w-full mt-6 bg-primary text-on-primary font-semibold py-3 rounded-xl hover:bg-[#0053db] transition-all shadow text-sm flex items-center justify-center gap-1.5">
    Create workspace
    <span class="material-symbols-outlined" style="font-size:16px">arrow_forward</span>
  </button>
</form>

<p class="text-center text-sm text-on-surface-variant mt-5">
  Already have a workspace?
  <a href="<?= url('/login') ?>" class="text-primary font-semibold hover:underline">Sign in</a>
</p>

<script>
function checkPasswordMatch() {
  var pw  = document.getElementById('reg-password').value;
  var cfm = document.getElementById('reg-confirm').value;
  var msg = document.getElementById('pw-match-msg');
  if (!cfm) { msg.className = 'text-xs mt-1.5 hidden'; return; }
  if (pw === cfm) {
    msg.textContent = 'Passwords match';
    msg.className   = 'text-xs mt-1.5 text-emerald-600';
  } else {
    msg.textContent = 'Passwords do not match';
    msg.className   = 'text-xs mt-1.5 text-red-600';
  }
}
</script>
