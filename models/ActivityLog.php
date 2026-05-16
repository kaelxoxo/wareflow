<?php
class ActivityLog {
    public static function log(int $tenantId, ?int $userId, string $action, string $entityType = '', ?int $entityId = null, array $details = []): void {
        DB::insert(
            'INSERT INTO activity_logs (tenant_id, user_id, action, entity_type, entity_id, details, ip_address)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$tenantId, $userId, $action, $entityType, $entityId,
             $details ? json_encode($details) : null,
             $_SERVER['REMOTE_ADDR'] ?? null]
        );
    }

    public static function recent(int $tenantId, int $limit = 20): array {
        return DB::query(
            'SELECT al.*, u.name AS user_name FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             WHERE al.tenant_id = ? ORDER BY al.created_at DESC LIMIT ?',
            [$tenantId, $limit]
        );
    }

    public static function all(int $tenantId, int $page = 1): array {
        $sql = 'SELECT al.*, u.name AS user_name FROM activity_logs al
                LEFT JOIN users u ON u.id = al.user_id
                WHERE al.tenant_id = ?
                ORDER BY al.created_at DESC';
        return DB::paginate($sql, [$tenantId], $page);
    }
}
