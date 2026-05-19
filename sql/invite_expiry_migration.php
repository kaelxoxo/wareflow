<?php
/**
 * One-time migration — adds invite_expires_at column to users table.
 * Run via browser: http://localhost/warehouse_inventory_management/sql/invite_expiry_migration.php
 * DELETE this file after running.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/DB.php';

$col = DB::row("SHOW COLUMNS FROM users LIKE 'invite_expires_at'");
if ($col) {
    die("<b>Already migrated.</b> <code>invite_expires_at</code> column already exists. Delete this file.");
}

try {
    DB::execute("ALTER TABLE users ADD COLUMN invite_expires_at TIMESTAMP NULL DEFAULT NULL AFTER invite_token");
    $success = true;
    $error   = null;
} catch (PDOException $e) {
    $success = false;
    $error   = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head><title>Invite Expiry Migration</title><style>body{font-family:sans-serif;max-width:520px;margin:60px auto;padding:0 20px}code{background:#f3f4f6;padding:2px 6px;border-radius:4px}</style></head>
<body>
<?php if ($success): ?>
  <h2>✅ Migration complete</h2>
  <p>Column <code>invite_expires_at</code> added to <code>users</code> table.</p>
  <p>New invitations will now expire after <strong>7 days</strong>. Existing pending invites have no expiry and remain valid.</p>
  <p style="color:#dc2626;margin-top:24px"><strong>⚠ Delete this file now:</strong> <code>sql/invite_expiry_migration.php</code></p>
<?php else: ?>
  <h2>❌ Migration failed</h2>
  <p><code><?= htmlspecialchars($error) ?></code></p>
<?php endif; ?>
</body>
</html>
