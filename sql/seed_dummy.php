<?php
// Wareflow — seed script: creates tenant, owner, warehouses, categories, items, stock movements
$pdo = new PDO('mysql:host=localhost;dbname=wareflow;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

// ── Tenant ──────────────────────────────────────────────────────────────────
$pdo->prepare("INSERT INTO tenants (name, slug, plan) VALUES (?, ?, ?)")
    ->execute(['Melendres Logistics', 'melendres-logistics', 'pro']);
$tenantId = $pdo->lastInsertId();
echo "Tenant ID: $tenantId\n";

// ── Owner ────────────────────────────────────────────────────────────────────
$pdo->prepare("INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?,?,?,?,?,?)")
    ->execute([$tenantId, 'Patrick Melendres', 'admin@wareflow.com', password_hash('password123', PASSWORD_BCRYPT), 'owner', 'active']);
$ownerId = $pdo->lastInsertId();
echo "Owner ID: $ownerId\n";

// ── Extra users ───────────────────────────────────────────────────────────────
$users = [
    ['Ana Reyes',    'ana@wareflow.com',   'admin'],
    ['Ben Cruz',     'ben@wareflow.com',   'manager'],
    ['Claire Delos', 'claire@wareflow.com','viewer'],
];
$userIds = [$ownerId];
foreach ($users as $u) {
    $pdo->prepare("INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?,?,?,?,?,?)")
        ->execute([$tenantId, $u[0], $u[1], password_hash('password123', PASSWORD_BCRYPT), $u[2], 'active']);
    $userIds[] = $pdo->lastInsertId();
}

// ── Warehouses ────────────────────────────────────────────────────────────────
$warehouses = [
    ['Main Warehouse',   'WH-01', 'Davao City, Philippines',     5000],
    ['North Depot',      'WH-02', 'Cagayan de Oro, Philippines', 3000],
    ['South Storage',    'WH-03', 'General Santos, Philippines', 2000],
];
$whIds = [];
foreach ($warehouses as $w) {
    $pdo->prepare("INSERT INTO warehouses (tenant_id, name, code, location, capacity, manager_id, status) VALUES (?,?,?,?,?,?,?)")
        ->execute([$tenantId, $w[0], $w[1], $w[2], $w[3], $userIds[array_search($w, $warehouses) + 1] ?? $ownerId, 'active']);
    $whIds[] = $pdo->lastInsertId();
}
echo "Warehouses: " . implode(', ', $whIds) . "\n";

// ── Categories ────────────────────────────────────────────────────────────────
$categories = [
    ['Electronics',    '#3b82f6'],
    ['Office Supplies','#10b981'],
    ['Furniture',      '#f59e0b'],
    ['Tools',          '#ef4444'],
    ['Packaging',      '#8b5cf6'],
    ['Cleaning',       '#06b6d4'],
];
$catIds = [];
foreach ($categories as $c) {
    $pdo->prepare("INSERT INTO categories (tenant_id, name, color) VALUES (?,?,?)")
        ->execute([$tenantId, $c[0], $c[1]]);
    $catIds[] = $pdo->lastInsertId();
}
echo "Categories: " . implode(', ', $catIds) . "\n";

// ── Items ─────────────────────────────────────────────────────────────────────
$items = [
    // [sku, name, desc, cat_idx, wh_idx, qty, price, reorder, unit, status]
    ['EL-001', 'Laptop Pro 15"',          'High-performance laptop',          0, 0, 25,  55000.00, 5,  'pcs',  'active'],
    ['EL-002', 'Mechanical Keyboard',     'TKL RGB mechanical keyboard',      0, 0, 40,  2500.00,  10, 'pcs',  'active'],
    ['EL-003', 'USB-C Hub 7-in-1',        'Multi-port USB-C hub',             0, 1, 60,  1200.00,  15, 'pcs',  'active'],
    ['EL-004', 'Wireless Mouse',          'Ergonomic wireless mouse',         0, 0, 35,  850.00,   10, 'pcs',  'active'],
    ['EL-005', '27" Monitor 4K',          '4K IPS display monitor',           0, 2, 12,  18000.00, 3,  'pcs',  'active'],
    ['OS-001', 'A4 Bond Paper (500s)',     'Multi-purpose bond paper ream',    1, 0, 120, 250.00,   30, 'ream', 'active'],
    ['OS-002', 'Ballpen Blue (box)',       'Blue ballpen box of 12',           1, 0, 80,  85.00,    20, 'box',  'active'],
    ['OS-003', 'Stapler Heavy Duty',       '24/6 heavy-duty stapler',          1, 1, 45,  320.00,   10, 'pcs',  'active'],
    ['OS-004', 'Correction Tape (6pk)',    '5mm correction tape pack',         1, 0, 60,  150.00,   15, 'pack', 'active'],
    ['OS-005', 'File Folder (100s)',       'Pressboard file folder pack',      1, 2, 30,  420.00,   8,  'pack', 'active'],
    ['FN-001', 'Office Chair Ergonomic',  'Lumbar support mesh chair',        2, 0, 8,   8500.00,  2,  'pcs',  'active'],
    ['FN-002', 'Standing Desk 120cm',     'Height adjustable standing desk',  2, 1, 5,   15000.00, 2,  'pcs',  'active'],
    ['FN-003', 'Steel Cabinet 4-drawer',  'Metal filing cabinet',             2, 2, 10,  6500.00,  2,  'pcs',  'active'],
    ['TL-001', 'Power Drill 18V',         'Brushless cordless drill',         3, 0, 15,  4200.00,  3,  'pcs',  'active'],
    ['TL-002', 'Tape Measure 5m',         'Self-lock measuring tape',         3, 0, 30,  280.00,   8,  'pcs',  'active'],
    ['TL-003', 'Angle Grinder 4"',        '850W angle grinder',               3, 1, 10,  2800.00,  3,  'pcs',  'active'],
    ['TL-004', 'Ladder 6ft Aluminum',    'A-frame aluminum ladder',           3, 2, 7,   3500.00,  2,  'pcs',  'active'],
    ['PK-001', 'Bubble Wrap Roll 50m',    '50m bubble wrap roll',             4, 0, 25,  650.00,   5,  'roll', 'active'],
    ['PK-002', 'Corrugated Box Medium',   '30x30x30cm shipping box',          4, 0, 200, 35.00,    50, 'pcs',  'active'],
    ['PK-003', 'Packing Tape 48mm',       '100m heavy-duty packing tape',     4, 1, 150, 95.00,    30, 'roll', 'active'],
    ['CL-001', 'Floor Mop Set',           'Bucket + spin mop',                5, 0, 20,  850.00,   5,  'set',  'active'],
    ['CL-002', 'Disinfectant 4L Refill',  'Hospital-grade disinfectant',      5, 1, 40,  380.00,   10, 'jug',  'active'],
    ['CL-003', 'Trash Bags Large (50s)',  '60L heavy-duty garbage bags',      5, 2, 60,  180.00,   15, 'pack', 'active'],
    ['EL-006', 'Projector 3500 Lumens',   'HDMI + wireless projector',        0, 1, 4,   28000.00, 1,  'pcs',  'active'],
    ['EL-007', 'Network Switch 24-port',  'Gigabit managed switch',           0, 0, 6,   12000.00, 2,  'pcs',  'active'],
    ['OS-006', 'Whiteboard Marker (12s)', 'Assorted color markers',           1, 0, 50,  220.00,   12, 'box',  'inactive'],
    ['TL-005', 'Safety Helmet',           'ANSI Z89.1 certified',             3, 2, 3,   450.00,   5,  'pcs',  'active'],
    ['PK-004', 'Stretch Film Roll',       '500m machine stretch film',        4, 0, 18,  720.00,   4,  'roll', 'active'],
];

$itemIds = [];
foreach ($items as $it) {
    $pdo->prepare(
        "INSERT INTO items (tenant_id, sku, name, description, category_id, warehouse_id,
         quantity, unit_price, reorder_point, unit, status, created_by)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
    )->execute([
        $tenantId, $it[0], $it[1], $it[2],
        $catIds[$it[3]], $whIds[$it[4]],
        $it[5], $it[6], $it[7], $it[8], $it[9], $ownerId,
    ]);
    $itemIds[] = $pdo->lastInsertId();
}
echo "Items: " . count($itemIds) . "\n";

// ── Stock Movements ────────────────────────────────────────────────────────────
$movements = [
    // [item_idx, from_wh, to_wh, type, qty, ref, notes, user_idx, days_ago]
    [0,  null, 0, 'in',         20, 'PO-2025-001', 'Initial stock arrival',              0, 30],
    [1,  null, 0, 'in',         35, 'PO-2025-002', 'Keyboard batch purchase',            0, 28],
    [5,  null, 0, 'in',        100, 'PO-2025-003', 'Paper supply Q1',                    1, 27],
    [6,  null, 0, 'in',         70, 'PO-2025-003', 'Ballpen batch',                      1, 27],
    [10, null, 0, 'in',          8, 'PO-2025-004', 'Chair delivery',                     0, 25],
    [13, null, 0, 'in',         12, 'PO-2025-005', 'Power drill stock',                  2, 24],
    [17, null, 0, 'in',         20, 'PO-2025-006', 'Packaging materials',                0, 22],
    [18, null, 0, 'in',        180, 'PO-2025-006', 'Box supply',                         0, 22],
    [0,  0,    1, 'transfer',    5, 'TR-2025-001', 'Transfer to North Depot',            0, 20],
    [2,  null, 1, 'in',         55, 'PO-2025-007', 'USB hub restock',                    1, 19],
    [20, null, 0, 'in',         15, 'PO-2025-008', 'Cleaning supplies arrival',          0, 18],
    [0,  0,    null,'out',       3, 'SO-2025-010', 'Sale to ABC Corp',                   0, 15],
    [1,  0,    null,'out',       8, 'SO-2025-011', 'Sale - keyboard sets',               0, 14],
    [5,  0,    null,'out',      25, 'SO-2025-012', 'Office paper sale',                  1, 13],
    [10, 0,    null,'out',       2, 'SO-2025-013', 'Chair sale',                         0, 12],
    [4,  null, 2, 'in',         10, 'PO-2025-009', 'Monitor stock — South Storage',      2, 11],
    [11, null, 1, 'in',          4, 'PO-2025-010', 'Standing desk order',                0, 10],
    [6,  0,    null,'out',      15, 'SO-2025-014', 'Ballpen bulk sale',                  1,  9],
    [13, 0,    null,'out',       3, 'SO-2025-015', 'Tool rental project',                2,  8],
    [18, 0,    null,'out',      40, 'SO-2025-016', 'Box sale to courier',                0,  7],
    [0,  0,    null,'adjustment',2, 'ADJ-001',     'Physical count correction',          0,  5],
    [3,  null, 0, 'in',         30, 'PO-2025-011', 'Mouse restock',                      0,  4],
    [19, null, 1, 'in',        120, 'PO-2025-012', 'Packing tape restock',               0,  3],
    [21, null, 1, 'in',         35, 'PO-2025-013', 'Disinfectant restock',               1,  2],
    [1,  0,    2, 'transfer',    5, 'TR-2025-002', 'Keyboard transfer to South',         0,  1],
    [9,  null, 2, 'in',         25, 'PO-2025-014', 'File folder south storage',          2,  1],
];

foreach ($movements as $m) {
    $daysAgo = $m[8];
    $createdAt = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));
    $pdo->prepare(
        "INSERT INTO stock_movements (tenant_id, item_id, from_warehouse_id, to_warehouse_id,
         movement_type, quantity, reference, notes, user_id, created_at)
         VALUES (?,?,?,?,?,?,?,?,?,?)"
    )->execute([
        $tenantId,
        $itemIds[$m[0]],
        isset($m[1]) ? ($m[1] !== null ? $whIds[$m[1]] : null) : null,
        isset($m[2]) ? ($m[2] !== null ? $whIds[$m[2]] : null) : null,
        $m[3], $m[4], $m[5], $m[6],
        $userIds[$m[7]],
        $createdAt,
    ]);
}
echo "Stock movements: " . count($movements) . "\n";

// ── Activity Logs ─────────────────────────────────────────────────────────────
$logs = [
    ['user.login',       'user',      $ownerId,  [],                                    7],
    ['item.created',     'item',      $itemIds[0], ['name'=>'Laptop Pro 15"'],           30],
    ['item.created',     'item',      $itemIds[5], ['name'=>'A4 Bond Paper'],            27],
    ['warehouse.created','warehouse', $whIds[1],  ['name'=>'North Depot'],              25],
    ['stock.in',         'item',      $itemIds[0], ['qty'=>20, 'ref'=>'PO-2025-001'],   30],
    ['item.updated',     'item',      $itemIds[10],['name'=>'Office Chair Ergonomic'],  12],
    ['user.login',       'user',      $userIds[1],  [],                                  3],
    ['stock.transfer',   'item',      $itemIds[0], ['qty'=>5,  'ref'=>'TR-2025-001'],   20],
    ['user.login',       'user',      $ownerId,  [],                                    1],
];
foreach ($logs as $l) {
    $createdAt = date('Y-m-d H:i:s', strtotime("-{$l[4]} days"));
    $pdo->prepare(
        "INSERT INTO activity_logs (tenant_id, user_id, action, entity_type, entity_id, details, ip_address, created_at)
         VALUES (?,?,?,?,?,?,?,?)"
    )->execute([
        $tenantId, $ownerId, $l[0], $l[1], $l[2],
        json_encode($l[3]), '127.0.0.1', $createdAt,
    ]);
}
echo "Activity logs: " . count($logs) . "\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

echo "\n✓ Done! Login at http://localhost/warehouse_inventory_management\n";
echo "  Email:    admin@wareflow.com\n";
echo "  Password: password123\n";
