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
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            flash('error', 'Email and password are required.');
            redirect('/login');
        }

        // Find user by email — look up tenant by email domain or allow tenant selection
        $user = User::byEmailAny($email);

        if (!$user || $user['status'] !== 'active' || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials.');
            set_old(['email' => $email]);
            redirect('/login');
        }

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

        $company  = trim($_POST['company']  ?? '');
        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];
        if (!$company) $errors[] = 'Company name is required.';
        if (!$name)    $errors[] = 'Your name is required.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if ($errors) {
            flash('error', implode(' ', $errors));
            set_old(compact('company', 'name', 'email'));
            redirect('/register');
        }

        // Check if email already exists globally (first tenant creation)
        // For multi-tenant, same email can exist in different tenants, but block same email same tenant
        $tenantId = Tenant::create($company);
        $userId   = User::create($tenantId, [
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role'          => 'owner',
            'status'        => 'active',
        ]);

        // Seed default warehouse and categories
        Warehouse::create($tenantId, ['name' => 'Main Warehouse', 'code' => 'WH-01', 'status' => 'active']);
        DB::insert('INSERT INTO categories (tenant_id, name, color) VALUES (?,?,?)', [$tenantId, 'General', '#6b7280']);

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
        $user = User::byToken($token);
        if (!$user) {
            flash('error', 'Invalid or expired invite link.');
            redirect('/login');
        }
        view('auth/invite', ['title' => 'Accept Invitation', 'invitedUser' => $user], 'auth');
    }

    public function inviteAccept(): void {
        Auth::start();
        Auth::verifyCsrf();

        $token    = $_POST['token']    ?? '';
        $name     = trim($_POST['name']     ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $invitedUser = User::byToken($token);
        if (!$invitedUser) {
            flash('error', 'Invalid or expired invite.');
            redirect('/login');
        }

        $errors = [];
        if (!$name) $errors[] = 'Name is required.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';

        if ($errors) {
            flash('error', implode(' ', $errors));
            redirect('/invite/' . $token);
        }

        User::acceptInvite($invitedUser['id'], $name, $password);
        $user = User::find($invitedUser['id'], $invitedUser['tenant_id']);
        Auth::login($user);
        ActivityLog::log($user['tenant_id'], $user['id'], 'user.invite_accepted');
        flash('success', 'Welcome to the team, ' . $name . '!');
        redirect('/dashboard');
    }
}
