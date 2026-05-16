<?php
class Category {
    public static function all(int $tenantId): array {
        return DB::query(
            'SELECT c.*, COUNT(i.id) AS item_count FROM categories c
             LEFT JOIN items i ON i.category_id = c.id AND i.tenant_id = c.tenant_id
             WHERE c.tenant_id = ? GROUP BY c.id ORDER BY c.name',
            [$tenantId]
        );
    }

    public static function find(int $id, int $tenantId): ?array {
        return DB::row('SELECT * FROM categories WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function create(int $tenantId, string $name, string $color = '#6b7280'): string {
        return DB::insert('INSERT INTO categories (tenant_id, name, color) VALUES (?, ?, ?)', [$tenantId, $name, $color]);
    }

    public static function delete(int $id, int $tenantId): void {
        DB::execute('DELETE FROM categories WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }
}
