<?php
class CustomField {
    public static function all(int $tenantId, string $entityType = 'item'): array {
        return DB::query(
            'SELECT * FROM custom_fields WHERE tenant_id = ? AND entity_type = ? ORDER BY sort_order, id',
            [$tenantId, $entityType]
        );
    }

    public static function find(int $id, int $tenantId): ?array {
        return DB::row('SELECT * FROM custom_fields WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function create(int $tenantId, array $d): string {
        $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($d['label']));
        $key = 'cf_' . trim($key, '_');
        $i = 2;
        $base = $key;
        while (DB::row('SELECT id FROM custom_fields WHERE field_key = ? AND tenant_id = ? AND entity_type = ?',
               [$key, $tenantId, $d['entity_type'] ?? 'item'])) {
            $key = $base . '_' . $i++;
        }
        $order = (int) DB::scalar('SELECT COALESCE(MAX(sort_order),0)+1 FROM custom_fields WHERE tenant_id = ?', [$tenantId]);
        return DB::insert(
            'INSERT INTO custom_fields (tenant_id, entity_type, label, field_key, field_type, options, required, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$tenantId, $d['entity_type'] ?? 'item', $d['label'], $key,
             $d['field_type'], isset($d['options']) ? json_encode(array_filter(array_map('trim', $d['options']))) : null,
             (int)!empty($d['required']), $order]
        );
    }

    public static function delete(int $id, int $tenantId): void {
        DB::execute('DELETE FROM custom_fields WHERE id = ? AND tenant_id = ?', [$id, $tenantId]);
    }

    public static function valuesFor(int $entityId, int $tenantId, string $entityType = 'item'): array {
        $rows = DB::query(
            'SELECT cf.*, cfv.value FROM custom_fields cf
             LEFT JOIN custom_field_values cfv ON cfv.custom_field_id = cf.id AND cfv.entity_id = ?
             WHERE cf.tenant_id = ? AND cf.entity_type = ? ORDER BY cf.sort_order',
            [$entityId, $tenantId, $entityType]
        );
        $out = [];
        foreach ($rows as $r) {
            $r['options'] = $r['options'] ? json_decode($r['options'], true) : [];
            $out[] = $r;
        }
        return $out;
    }

    public static function saveValues(int $entityId, int $tenantId, array $values): void {
        foreach ($values as $fieldId => $value) {
            $existing = DB::row(
                'SELECT id FROM custom_field_values WHERE custom_field_id = ? AND entity_id = ?',
                [$fieldId, $entityId]
            );
            if ($existing) {
                DB::execute('UPDATE custom_field_values SET value = ? WHERE custom_field_id = ? AND entity_id = ?',
                    [$value, $fieldId, $entityId]);
            } else {
                DB::insert('INSERT INTO custom_field_values (tenant_id, custom_field_id, entity_id, value) VALUES (?,?,?,?)',
                    [$tenantId, $fieldId, $entityId, $value]);
            }
        }
    }
}
