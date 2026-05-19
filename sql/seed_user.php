<?php
/**
 * One-time seed script — creates a demo user with full dummy data.
 * Run via browser: http://localhost/warehouse_inventory_management/sql/seed_user.php
 * DELETE this file after running.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/DB.php';

$pdo = DB::get();

// ── Config ────────────────────────────────────────────────────────────────
$ownerName  = 'Demo User';
$ownerEmail = 'demo@wareflow.test';
$ownerPass  = 'password123';
$tenantName = 'Demo Company';
// ─────────────────────────────────────────────────────────────────────────

// Guard: prevent re-running
$existing = DB::row('SELECT id FROM users WHERE email = ?', [$ownerEmail]);
if ($existing) {
    die("<b>Already seeded.</b> User <code>{$ownerEmail}</code> already exists. Delete this file.");
}

$pdo->beginTransaction();
try {
    // Tenant
    $slug = 'demo-company';
    $i = 2;
    while (DB::row('SELECT id FROM tenants WHERE slug = ?', [$slug])) {
        $slug = 'demo-company-' . $i++;
    }
    $pdo->prepare('INSERT INTO tenants (name, slug, plan) VALUES (?, ?, ?)')->execute([$tenantName, $slug, 'pro']);
    $tid = (int)$pdo->lastInsertId();

    // Owner
    $hash = password_hash($ownerPass, PASSWORD_BCRYPT);
    $pdo->prepare('INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$tid, $ownerName, $ownerEmail, $hash, 'owner', 'active']);
    $uid = (int)$pdo->lastInsertId();

    // Team members
    $memberHash = password_hash('password', PASSWORD_BCRYPT);
    $pdo->prepare('INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$tid, 'Marco Dela Cruz', 'marco@wareflow.test', $memberHash, 'manager', 'active']);
    $pdo->prepare('INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)')
        ->execute([$tid, 'Nina Ramos', 'nina@wareflow.test', $memberHash, 'viewer', 'active']);

    // Warehouses
    $pdo->prepare('INSERT INTO warehouses (tenant_id, name, code, location, capacity, status, manager_id) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$tid, 'Main Warehouse', 'WH-MAIN', 'Davao City, Building A', 5000, 'active', $uid]);
    $wh1 = (int)$pdo->lastInsertId();

    $pdo->prepare('INSERT INTO warehouses (tenant_id, name, code, location, capacity, status, manager_id) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$tid, 'Annex Storage', 'WH-ANX', 'Davao City, Building B', 2000, 'active', null]);
    $wh2 = (int)$pdo->lastInsertId();

    // Categories
    $cats = [
        ['Electronics',    '#3b82f6'],
        ['Office Supplies', '#10b981'],
        ['Furniture',      '#f59e0b'],
        ['Cleaning',       '#8b5cf6'],
        ['Packaging',      '#ef4444'],
    ];
    $catIds = [];
    foreach ($cats as [$name, $color]) {
        $pdo->prepare('INSERT INTO categories (tenant_id, name, color) VALUES (?, ?, ?)')->execute([$tid, $name, $color]);
        $catIds[$name] = (int)$pdo->lastInsertId();
    }

    // Items
    $items = [
        ['ELEC-001', 'Laptop Dell Inspiron 15',     '15.6" Intel i5, 8GB RAM, 512GB SSD',     'Electronics',    $wh1, 12,  42500.00, 3,  'pcs'],
        ['ELEC-002', 'Wireless Mouse Logitech M235', 'USB nano receiver, 12-month battery',    'Electronics',    $wh1, 45,    895.00, 10, 'pcs'],
        ['ELEC-003', 'USB-C Hub 7-in-1',             'HDMI, USB3.0 x3, SD card, PD charging', 'Electronics',    $wh1, 8,   1250.00, 5,  'pcs'],
        ['ELEC-004', 'Brother Printer HL-L2350',     'Monochrome laser, WiFi',                 'Electronics',    $wh2, 4,   7800.00, 2,  'pcs'],
        ['OFFC-001', 'Ballpen Box Black',             'Kilometrico, box of 50',                 'Office Supplies',$wh1, 120,  285.00, 20, 'box'],
        ['OFFC-002', 'A4 Bond Paper 500 sheets',     '80gsm white',                            'Office Supplies',$wh1, 200,  280.00, 50, 'ream'],
        ['OFFC-003', 'Stapler Desk Heavy Duty',      '25-sheet capacity',                      'Office Supplies',$wh2, 15,   450.00, 5,  'pcs'],
        ['FURN-001', 'Office Chair Ergonomic',       'Mesh back, adjustable armrests',         'Furniture',      $wh2, 6,   6500.00, 2,  'pcs'],
        ['FURN-002', 'Folding Table 6ft',            'Heavy-duty steel frame',                 'Furniture',      $wh2, 3,   3200.00, 2,  'pcs'],
        ['CLEN-001', 'Floor Wax Liquid 1L',          'Clear gloss finish',                     'Cleaning',       $wh1, 5,    320.00, 10, 'btl'],
        ['CLEN-002', 'Disinfectant Spray 500ml',     'Multi-surface use',                      'Cleaning',       $wh1, 30,   185.00, 10, 'pcs'],
        ['PACK-001', 'Bubble Wrap Roll 50m',         '30cm wide, small bubbles',               'Packaging',      $wh2, 18,   650.00, 5,  'roll'],
        ['PACK-002', 'Carton Box Medium (25pcs)',     '40x30x25cm',                             'Packaging',      $wh2, 60,   480.00, 15, 'pack'],
    ];

    $itemIds = [];
    $stmt = $pdo->prepare('INSERT INTO items (tenant_id, sku, name, description, category_id, warehouse_id, quantity, unit_price, reorder_point, unit, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($items as [$sku, $name, $desc, $cat, $wh, $qty, $price, $reorder, $unit]) {
        $stmt->execute([$tid, $sku, $name, $desc, $catIds[$cat], $wh, $qty, $price, $reorder, $unit, 'active', $uid]);
        $itemIds[$sku] = (int)$pdo->lastInsertId();
    }

    // Stock movements
    $movements = [
        [$itemIds['ELEC-001'], null, $wh1, 'in',       15, 'PO-2025-001', 'Initial stock from supplier',      30],
        [$itemIds['ELEC-001'], $wh1, null, 'out',        3, 'SO-2025-010', 'Issued to IT department',          20],
        [$itemIds['ELEC-002'], null, $wh1, 'in',        60, 'PO-2025-002', 'Bulk purchase',                    25],
        [$itemIds['ELEC-002'], $wh1, null, 'out',       15, 'SO-2025-011', 'Distributed to staff',             10],
        [$itemIds['OFFC-001'], null, $wh1, 'in',       150, 'PO-2025-003', 'Monthly office supply restock',    15],
        [$itemIds['OFFC-001'], $wh1, null, 'out',       30, 'SO-2025-015', 'Issued to admin',                   5],
        [$itemIds['OFFC-002'], null, $wh1, 'in',       250, 'PO-2025-004', 'Quarterly paper supply',           20],
        [$itemIds['OFFC-002'], $wh1, null, 'out',       50, 'SO-2025-016', 'Printing room restock',             3],
        [$itemIds['PACK-001'], null, $wh2, 'in',        20, 'PO-2025-005', 'Packaging materials',              12],
        [$itemIds['PACK-001'], $wh2, $wh1, 'transfer',   2, 'TRF-2025-001','Moved for urgent shipment',         2],
    ];

    $stmt = $pdo->prepare('INSERT INTO stock_movements (tenant_id, item_id, from_warehouse_id, to_warehouse_id, movement_type, quantity, reference, notes, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($movements as [$itemId, $from, $to, $type, $qty, $ref, $notes, $daysAgo]) {
        $stmt->execute([$tid, $itemId, $from, $to, $type, $qty, $ref, $notes, $uid, date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"))]);
    }

    // Activity logs
    $logs = [
        ['item.created',      'item', $itemIds['ELEC-001'], ['name' => 'Laptop Dell Inspiron 15',     'sku' => 'ELEC-001'], 30],
        ['item.created',      'item', $itemIds['ELEC-002'], ['name' => 'Wireless Mouse Logitech M235','sku' => 'ELEC-002'], 25],
        ['item.created',      'item', $itemIds['OFFC-001'], ['name' => 'Ballpen Box Black',           'sku' => 'OFFC-001'], 15],
        ['item.created',      'item', $itemIds['PACK-001'], ['name' => 'Bubble Wrap Roll 50m',        'sku' => 'PACK-001'], 12],
        ['stock.transferred', 'item', $itemIds['PACK-001'], ['qty' => 2, 'from' => 'WH-ANX', 'to' => 'WH-MAIN'],           2],
        ['user.invited',      'user', null,                 ['email' => 'marco@wareflow.test', 'role' => 'manager'],       28],
        ['user.invited',      'user', null,                 ['email' => 'nina@wareflow.test',  'role' => 'viewer'],        28],
    ];

    $stmt = $pdo->prepare('INSERT INTO activity_logs (tenant_id, user_id, action, entity_type, entity_id, details, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($logs as [$action, $entity, $entityId, $details, $daysAgo]) {
        $stmt->execute([$tid, $uid, $action, $entity, $entityId, json_encode($details), date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"))]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die('<b>Seed failed:</b> ' . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head><title>Seed Complete</title><style>body{font-family:sans-serif;max-width:520px;margin:60px auto;padding:0 20px}code{background:#f3f4f6;padding:2px 6px;border-radius:4px}table{border-collapse:collapse;width:100%}td,th{padding:8px 12px;border:1px solid #e5e7eb;text-align:left}th{background:#f9fafb}</style></head>
<body>
<h2>✅ Seed complete</h2>
<p>New tenant and demo user created successfully.</p>
<table>
  <tr><th>Field</th><th>Value</th></tr>
  <tr><td>Email</td><td><code><?= htmlspecialchars($ownerEmail) ?></code></td></tr>
  <tr><td>Password</td><td><code><?= htmlspecialchars($ownerPass) ?></code></td></tr>
  <tr><td>Tenant</td><td><?= htmlspecialchars($tenantName) ?></td></tr>
  <tr><td>Plan</td><td>Pro</td></tr>
  <tr><td>Warehouses</td><td>2</td></tr>
  <tr><td>Items</td><td>13</td></tr>
  <tr><td>Team members</td><td>2 (marco, nina — password: <code>password</code>)</td></tr>
</table>
<p style="margin-top:24px;color:#dc2626"><strong>⚠ Delete this file now:</strong> <code>sql/seed_user.php</code></p>
</body>
</html>
