<?php
class StockMovement {
    public static function all(int $tenantId, array $filters = [], int $page = 1): array {
        $where  = 'sm.tenant_id = ?';
        $params = [$tenantId];

        if (!empty($filters['item_id'])) {
            $where .= ' AND sm.item_id = ?';
            $params[] = $filters['item_id'];
        }
        if (!empty($filters['type'])) {
            $where .= ' AND sm.movement_type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['warehouse_id'])) {
            $where .= ' AND (sm.from_warehouse_id = ? OR sm.to_warehouse_id = ?)';
            $params[] = $filters['warehouse_id'];
            $params[] = $filters['warehouse_id'];
        }
        if (!empty($filters['date_from'])) {
            $where .= ' AND DATE(sm.created_at) >= ?';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where .= ' AND DATE(sm.created_at) <= ?';
            $params[] = $filters['date_to'];
        }

        $sql = "SELECT sm.*, i.name AS item_name, i.sku,
                       fw.name AS from_warehouse, tw.name AS to_warehouse,
                       u.name AS user_name
                FROM stock_movements sm
                JOIN items i ON i.id = sm.item_id
                LEFT JOIN warehouses fw ON fw.id = sm.from_warehouse_id
                LEFT JOIN warehouses tw ON tw.id = sm.to_warehouse_id
                JOIN users u ON u.id = sm.user_id
                WHERE {$where}
                ORDER BY sm.created_at DESC";

        return DB::paginate($sql, $params, $page);
    }

    public static function transfer(int $tenantId, array $d, int $userId): void {
        $db = DB::get();
        $db->beginTransaction();
        try {
            $type  = $d['movement_type'];
            $itemId = (int)$d['item_id'];
            $qty   = (int)$d['quantity'];

            // Validate item belongs to tenant
            $item = DB::row('SELECT * FROM items WHERE id = ? AND tenant_id = ?', [$itemId, $tenantId]);
            if (!$item) throw new Exception('Item not found.');

            // Update item quantity
            if ($type === 'in') {
                DB::execute('UPDATE items SET quantity = quantity + ? WHERE id = ? AND tenant_id = ?',
                    [$qty, $itemId, $tenantId]);
                // Update warehouse_id if provided
                if (!empty($d['to_warehouse_id'])) {
                    DB::execute('UPDATE items SET warehouse_id = ? WHERE id = ? AND tenant_id = ?',
                        [$d['to_warehouse_id'], $itemId, $tenantId]);
                }
            } elseif ($type === 'out') {
                if ($item['quantity'] < $qty) throw new Exception('Insufficient stock.');
                DB::execute('UPDATE items SET quantity = quantity - ? WHERE id = ? AND tenant_id = ?',
                    [$qty, $itemId, $tenantId]);
            } elseif ($type === 'transfer') {
                if ($item['quantity'] < $qty) throw new Exception('Insufficient stock for transfer.');
                DB::execute('UPDATE items SET warehouse_id = ?, quantity = quantity WHERE id = ? AND tenant_id = ?',
                    [$d['to_warehouse_id'], $itemId, $tenantId]);
            } elseif ($type === 'adjustment') {
                $newQty = (int)$d['new_quantity'];
                DB::execute('UPDATE items SET quantity = ? WHERE id = ? AND tenant_id = ?',
                    [$newQty, $itemId, $tenantId]);
                $qty = abs($newQty - $item['quantity']);
            }

            DB::insert(
                'INSERT INTO stock_movements (tenant_id,item_id,from_warehouse_id,to_warehouse_id,
                  movement_type,quantity,reference,notes,user_id)
                 VALUES (?,?,?,?,?,?,?,?,?)',
                [$tenantId, $itemId,
                 $d['from_warehouse_id'] ?: null,
                 $d['to_warehouse_id']   ?: null,
                 $type, $qty,
                 $d['reference'] ?? null,
                 $d['notes']     ?? null,
                 $userId]
            );

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function recentByTenant(int $tenantId, int $limit = 10): array {
        return DB::query(
            "SELECT sm.*, i.name AS item_name, i.sku,
                    fw.name AS from_warehouse, tw.name AS to_warehouse,
                    u.name AS user_name
             FROM stock_movements sm
             JOIN items i ON i.id = sm.item_id
             LEFT JOIN warehouses fw ON fw.id = sm.from_warehouse_id
             LEFT JOIN warehouses tw ON tw.id = sm.to_warehouse_id
             JOIN users u ON u.id = sm.user_id
             WHERE sm.tenant_id = ?
             ORDER BY sm.created_at DESC LIMIT ?",
            [$tenantId, $limit]
        );
    }
}
