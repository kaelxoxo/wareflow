<?php
class Auth {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function guard(string $permission = ''): void {
        self::start();
        if (!self::check()) {
            redirect('/login');
        }
        if ($permission && !self::can($permission)) {
            http_response_code(403);
            view('errors/403', [], 'app');
            exit();
        }
    }

    public static function check(): bool {
        return !empty($_SESSION['user_id']) && !empty($_SESSION['tenant_id']);
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        static $cached = null;
        if ($cached) return $cached;
        $cached = DB::row(
            'SELECT u.*, t.name AS tenant_name, t.slug AS tenant_slug, t.plan AS tenant_plan
             FROM users u JOIN tenants t ON t.id = u.tenant_id
             WHERE u.id = ? AND u.tenant_id = ? AND u.status = "active"',
            [self::id(), self::tenantId()]
        );
        return $cached;
    }

    public static function id(): ?int {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    public static function tenantId(): ?int {
        return isset($_SESSION['tenant_id']) ? (int)$_SESSION['tenant_id'] : null;
    }

    public static function role(): string {
        return $_SESSION['role'] ?? 'viewer';
    }

    public static function can(string $permission): bool {
        $role = self::role();
        $map  = [
            'owner'   => ['*'],
            'admin'   => ['manage_users','manage_inventory','view_inventory','manage_warehouses','view_warehouses','view_reports','manage_settings','manage_custom_fields'],
            'manager' => ['manage_inventory','view_inventory','manage_warehouses','view_warehouses','view_reports','manage_custom_fields'],
            'viewer'  => ['view_inventory','view_warehouses','view_reports'],
        ];
        $perms = $map[$role] ?? [];
        return in_array('*', $perms, true) || in_array($permission, $perms, true);
    }

    public static function login(array $user): void {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['tenant_id'] = $user['tenant_id'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['_csrf']     = bin2hex(random_bytes(32));
        DB::execute('UPDATE users SET last_login = NOW() WHERE id = ?', [$user['id']]);
    }

    public static function logout(): void {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function csrf(): string {
        self::start();
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verifyCsrf(): void {
        $token = $_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!isset($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
            http_response_code(419);
            die('CSRF token mismatch.');
        }
    }
}
