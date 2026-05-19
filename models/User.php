<?php
class User {
    private static array $UPDATABLE = ['name', 'email', 'password_hash', 'role', 'status', 'invite_token', 'invite_expires_at'];

    public static function create(int $tenantId, array $d): string {
        return DB::insert(
            'INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)',
            [$tenantId, $d['name'], $d['email'], $d['password_hash'], $d['role'] ?? 'viewer', $d['status'] ?? 'active']
        );
    }

    public static function all(int $tenantId): array {
        return DB::query(
            'SELECT u.*, w.name AS warehouse_name FROM users u
             LEFT JOIN warehouses w ON w.manager_id = u.id AND w.tenant_id = u.tenant_id
             WHERE u.tenant_id = ? ORDER BY u.created_at DESC',
            [$tenantId]
        );
    }

    public static function find(int $id, int $tenantId): ?array {
        return DB::row('SELECT * FROM users WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function byEmail(string $email, int $tenantId): ?array {
        return DB::row('SELECT * FROM users WHERE email = ? AND tenant_id = ?', [$email, $tenantId]);
    }

    public static function byEmailAny(string $email): ?array {
        // Only match active users — invited/suspended users cannot log in
        return DB::row("SELECT * FROM users WHERE email = ? AND status = 'active' ORDER BY id LIMIT 1", [$email]);
    }

    public static function update(int $id, int $tenantId, array $d): void {
        // Only allow explicitly whitelisted columns to prevent mass-assignment
        $d = array_intersect_key($d, array_flip(self::$UPDATABLE));
        if (empty($d)) return;
        $sets = []; $params = [];
        foreach ($d as $k => $v) { $sets[] = "`{$k}` = ?"; $params[] = $v; }
        $params[] = $id; $params[] = $tenantId;
        DB::execute('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ? AND tenant_id = ?', $params);
    }

    public static function delete(int $id, int $tenantId): void {
        DB::execute('DELETE FROM users WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function invite(int $tenantId, string $email, string $role): array {
        $token   = bin2hex(random_bytes(32));
        $name    = explode('@', $email)[0];
        $expires = date('Y-m-d H:i:s', strtotime('+7 days'));
        $id = DB::insert(
            'INSERT INTO users (tenant_id, name, email, password_hash, role, status, invite_token, invite_expires_at)
             VALUES (?, ?, ?, ?, ?, "invited", ?, ?)',
            [$tenantId, $name, $email, '', $role, $token, $expires]
        );
        return ['id' => $id, 'token' => $token];
    }

    public static function byToken(string $token): ?array {
        return DB::row("SELECT * FROM users WHERE invite_token = ? AND status = 'invited'", [$token]);
    }

    // Looks up a token regardless of status — used to distinguish "used" from "invalid"
    public static function byTokenAny(string $token): ?array {
        return DB::row('SELECT * FROM users WHERE invite_token = ?', [$token]);
    }

    public static function acceptInvite(int $id, string $name, string $pass): void {
        // Keep invite_token so we can show "already accepted" message if link reused
        // Add AND status = 'invited' to prevent double-acceptance race condition
        DB::execute(
            "UPDATE users SET name = ?, password_hash = ?, status = 'active', invite_expires_at = NULL
             WHERE id = ? AND status = 'invited'",
            [$name, password_hash($pass, PASSWORD_BCRYPT), $id]
        );
    }

    public static function count(int $tenantId): int {
        return (int) DB::scalar('SELECT COUNT(*) FROM users WHERE tenant_id = ?', [$tenantId]);
    }
}
