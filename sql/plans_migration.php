<?php
/**
 * One-time migration — adds 'max' plan tier to tenants.plan ENUM.
 * Run via browser: http://localhost/warehouse_inventory_management/sql/plans_migration.php
 * DELETE this file after running.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/DB.php';

// Guard: check if already migrated
$col  = DB::row("SHOW COLUMNS FROM tenants LIKE 'plan'");
$type = $col['Type'] ?? '';
if (stripos($type, 'max') !== false) {
    die("<b>Already migrated.</b> <code>max</code> already exists in the plan ENUM. Delete this file.");
}

try {
    DB::execute("ALTER TABLE tenants MODIFY plan ENUM('starter','pro','max') DEFAULT 'starter'");
    $success = true;
    $error   = null;
} catch (PDOException $e) {
    $success = false;
    $error   = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head><title>Plans Migration</title><style>body{font-family:sans-serif;max-width:520px;margin:60px auto;padding:0 20px}code{background:#f3f4f6;padding:2px 6px;border-radius:4px}table{border-collapse:collapse;width:100%}td,th{padding:8px 12px;border:1px solid #e5e7eb;text-align:left}th{background:#f9fafb}</style></head>
<body>
<?php if ($success): ?>
<h2>✅ Migration complete</h2>
<p>The <code>tenants.plan</code> column now supports the following values:</p>
<table>
  <tr><th>Value</th><th>Description</th></tr>
  <tr><td><code>starter</code></td><td>Free tier (default)</td></tr>
  <tr><td><code>pro</code></td><td>Pro plan — $5/month</td></tr>
  <tr><td><code>max</code></td><td>Max plan — $10/month</td></tr>
</table>
<p style="margin-top:24px;color:#dc2626"><strong>⚠ Delete this file now:</strong> <code>sql/plans_migration.php</code></p>
<?php else: ?>
<h2>❌ Migration failed</h2>
<p><code><?= htmlspecialchars($error) ?></code></p>
<?php endif; ?>
</body>
</html>
