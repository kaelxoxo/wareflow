<?php
class AuthController {
    public function loginForm(): void {
        if (Auth::check()) redirect('/dashboard');
        view('auth/login', ['title' => 'Sign In'], 'auth');
    }

    public function login(): void {
        Auth::start();
        Auth::verifyCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password']   ?? '';

        if (!$email || !$password) {
            flash('error', 'Email and password are required.');
            redirect('/login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Invalid credentials.');
            redirect('/login');
        }

        $user = User::byEmailAny($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials.');
            set_old(['email' => $email]);
            redirect('/login');
        }

        // byEmailAny only returns active users — no need to check status again
        Auth::login($user);
        ActivityLog::log($user['tenant_id'], $user['id'], 'user.login');
        flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('/dashboard');
    }

    public function registerForm(): void {
        if (Auth::check()) redirect('/dashboard');
        view('auth/register', ['title' => 'Create your workspace'], 'auth');
    }

    public function register(): void {
        Auth::start();
        Auth::verifyCsrf();

        $company  = trim($_POST['company']           ?? '');
        $name     = trim($_POST['name']              ?? '');
        $email    = trim($_POST['email']             ?? '');
        $password = $_POST['password']               ?? '';
        $confirm  = $_POST['password_confirm']       ?? '';

        $errors = [];
        if (!$company || strlen($company) > 100) $errors[] = 'Company name must be 1–100 characters.';
        if (!$name    || strlen($name)    > 100) $errors[] = 'Your name must be 1–100 characters.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150)
            $errors[] = 'A valid email address is required (max 150 characters).';
        if (strlen($password) < 8)  $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if ($errors) {
            flash('error', implode(' ', $errors));
            set_old(compact('company', 'name', 'email'));
            redirect('/register');
        }

        $db = DB::get();
        $db->beginTransaction();
        try {
            $tenantId = Tenant::create($company);
            $userId   = User::create($tenantId, [
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'role'          => 'owner',
                'status'        => 'active',
            ]);
            Warehouse::create($tenantId, ['name' => 'Main Warehouse', 'code' => 'WH-01', 'status' => 'active']);
            DB::insert('INSERT INTO categories (tenant_id, name, color) VALUES (?,?,?)', [$tenantId, 'General', '#6b7280']);
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            flash('error', 'Could not create workspace. Please try again.');
            set_old(compact('company', 'name', 'email'));
            redirect('/register');
        }

        $user = User::find($userId, $tenantId);
        Auth::login($user);
        ActivityLog::log($tenantId, $userId, 'tenant.created', 'tenant', $tenantId, ['company' => $company]);
        flash('success', 'Workspace created! Welcome to Wareflow.');
        redirect('/dashboard');
    }

    public function logout(): void {
        if (Auth::check()) {
            try { ActivityLog::log(Auth::tenantId(), Auth::id(), 'user.logout'); } catch (Throwable $e) {}
        }
        Auth::logout();
        redirect('/login');
    }

    public function inviteForm(string $token): void {
        // Already logged in — send to dashboard
        if (Auth::check()) {
            redirect('/dashboard');
        }

        if (!$token) {
            $this->showInviteError('invalid');
            return;
        }

        $invitedUser = User::byToken($token); // status = 'invited' only

        if (!$invitedUser) {
            // Token exists but was already used or was never valid
            $anyUser = User::byTokenAny($token);
            if ($anyUser) {
                $this->showInviteError($anyUser['status'] === 'active' ? 'accepted' : 'revoked');
            } else {
                $this->showInviteError('invalid');
            }
            return;
        }

        // Check expiry (column present only after migration)
        $expires = $invitedUser['invite_expires_at'] ?? null;
        if ($expires && strtotime($expires) < time()) {
            $this->showInviteError('expired');
            return;
        }

        $tenant = Tenant::find($invitedUser['tenant_id']);
        view('auth/invite', [
            'title'       => 'Accept Invitation',
            'invitedUser' => $invitedUser,
            'tenant'      => $tenant,
        ], 'auth');
    }

    public function inviteAccept(): void {
        Auth::start();
        Auth::verifyCsrf();

        $token    = $_POST['token']            ?? '';
        $name     = trim($_POST['name']        ?? '');
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        // Re-validate token server-side
        $invitedUser = User::byToken($token);
        if (!$invitedUser) {
            flash('error', 'This invite link is no longer valid.');
            redirect('/login');
        }

        $expires = $invitedUser['invite_expires_at'] ?? null;
        if ($expires && strtotime($expires) < time()) {
            flash('error', 'This invite has expired. Ask your workspace owner for a new invitation.');
            redirect('/login');
        }

        $errors = [];
        if (!$name)               $errors[] = 'Your name is required.';
        if (strlen($name) > 100)  $errors[] = 'Name must be 100 characters or less.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('/invite/' . rawurlencode($token));
        }

        User::acceptInvite($invitedUser['id'], $name, $password);

        // Re-fetch to confirm status changed (guards against race condition)
        $user = User::find($invitedUser['id'], $invitedUser['tenant_id']);
        if (!$user || $user['status'] !== 'active') {
            flash('error', 'Could not complete invite. Please try again or contact your workspace owner.');
            redirect('/login');
        }

        Auth::login($user);
        ActivityLog::log($user['tenant_id'], $user['id'], 'user.invite_accepted');
        flash('success', 'Welcome to the team, ' . $name . '!');
        redirect('/dashboard');
    }

    private function showInviteError(string $reason): void {
        $map = [
            'invalid'  => ['title' => 'Invalid Invite Link',     'icon' => 'link_off',      'msg' => 'This invite link is invalid or has already been used. Ask your workspace owner to send a new invitation.'],
            'accepted' => ['title' => 'Invite Already Accepted',  'icon' => 'check_circle',  'msg' => 'This invite has already been accepted. Sign in to access your workspace.'],
            'revoked'  => ['title' => 'Invite Revoked',           'icon' => 'block',         'msg' => 'This invite has been revoked. Contact your workspace owner if you believe this is a mistake.'],
            'expired'  => ['title' => 'Invite Expired',           'icon' => 'schedule',      'msg' => 'This invite link has expired — invites are valid for 7 days. Ask your workspace owner to send a new one.'],
        ];
        $r = $map[$reason] ?? $map['invalid'];
        view('auth/invite_error', ['title' => $r['title'], 'reason' => $reason, 'error' => $r], 'auth');
    }
}
