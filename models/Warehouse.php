<?php
class Warehouse {
    public static function all(int $tenantId, bool $activeOnly = false): array {
        $where = $activeOnly ? " AND w.status = 'active'" : '';
        return DB::query(
            "SELECT w.*, u.name AS manager_name,
                    COALESCE(SUM(i.quantity),0) AS total_items,
                    COALESCE(SUM(i.quantity * i.unit_price),0) AS total_value
             FROM warehouses w
             LEFT JOIN users u ON u.id = w.manager_id
             LEFT JOIN items i ON i.warehouse_id = w.id AND i.tenant_id = w.tenant_id
             WHERE w.tenant_id = ?{$where}
             GROUP BY w.id ORDER BY w.name",
            [$tenantId]
        );
    }

    public static function find(int $id, int $tenantId): ?array {
        return DB::row('SELECT * FROM warehouses WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function create(int $tenantId, array $d): string {
        return DB::insert(
            'INSERT INTO warehouses (tenant_id, name, code, location, capacity, manager_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$tenantId, $d['name'], $d['code'] ?? null, $d['location'] ?? null,
             (int)($d['capacity'] ?? 0), $d['manager_id'] ?: null, $d['status'] ?? 'active']
        );
    }

    public static function update(int $id, int $tenantId, array $d): void {
        DB::execute(
            'UPDATE warehouses SET name=?, code=?, location=?, capacity=?, manager_id=?, status=? WHERE id=? AND tenant_id=?',
            [$d['name'], $d['code'] ?? null, $d['location'] ?? null,
             (int)($d['capacity'] ?? 0), $d['manager_id'] ?: null,
             $d['status'] ?? 'active', $id, $tenantId]
        );
    }

    public static function delete(int $id, int $tenantId): bool {
        $hasItems = (int) DB::scalar('SELECT COUNT(*) FROM items WHERE warehouse_id = ? AND tenant_id = ?', [$id, $tenantId]);
        if ($hasItems > 0) return false;
        DB::execute('DELETE FROM warehouses WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
        return true;
    }

    public static function count(int $tenantId, bool $activeOnly = false): int {
        $where = $activeOnly ? " AND status = 'active'" : '';
        return (int) DB::scalar("SELECT COUNT(*) FROM warehouses WHERE tenant_id = ?{$where}", [$tenantId]);
    }
}
