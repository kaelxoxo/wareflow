<?php
/**
 * One-time installer — creates all Wareflow tables on a fresh database.
 * Run via browser: http://localhost/warehouse_inventory_management/sql/schema.php
 * DELETE this file after running.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../core/DB.php';

// Guard: abort if tables already exist
$existing = DB::row("SHOW TABLES LIKE 'tenants'");
if ($existing) {
    die("<b>Already installed.</b> The <code>tenants</code> table already exists. Delete this file.");
}

$pdo = DB::get();
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$pdo->exec('SET NAMES utf8mb4');

$statements = [
    "CREATE TABLE IF NOT EXISTS tenants (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(100)  NOT NULL,
        slug        VARCHAR(60)   NOT NULL,
        plan        ENUM('starter','pro','max') DEFAULT 'starter',
        settings    JSON          NULL,
        stripe_customer_id      VARCHAR(100) NULL,
        stripe_subscription_id  VARCHAR(100) NULL,
        subscription_status     ENUM('none','trialing','active','past_due','canceled','unpaid') DEFAULT 'none',
        subscription_period_end TIMESTAMP NULL,
        created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_slug (slug)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS users (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id     INT          NOT NULL,
        name          VARCHAR(100) NOT NULL,
        email         VARCHAR(150) NOT NULL,
        password_hash VARCHAR(255) NOT NULL DEFAULT '',
        role          ENUM('owner','admin','manager','viewer') DEFAULT 'viewer',
        status        ENUM('active','invited','suspended')     DEFAULT 'active',
        invite_token  VARCHAR(100) NULL,
        last_login    TIMESTAMP    NULL,
        created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_email_tenant (email, tenant_id),
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS warehouses (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id   INT          NOT NULL,
        name        VARCHAR(100) NOT NULL,
        code        VARCHAR(20)  NULL,
        location    VARCHAR(200) NULL,
        capacity    INT          DEFAULT 0,
        status      ENUM('active','inactive') DEFAULT 'active',
        manager_id  INT          NULL,
        created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tenant_id)  REFERENCES tenants(id)  ON DELETE CASCADE,
        FOREIGN KEY (manager_id) REFERENCES users(id)    ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS categories (
        id        INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id INT          NOT NULL,
        name      VARCHAR(100) NOT NULL,
        color     VARCHAR(10)  DEFAULT '#6b7280',
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS items (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id     INT             NOT NULL,
        sku           VARCHAR(80)     NOT NULL,
        name          VARCHAR(200)    NOT NULL,
        description   TEXT            NULL,
        category_id   INT             NULL,
        warehouse_id  INT             NULL,
        quantity      INT             DEFAULT 0,
        unit_price    DECIMAL(15,2)   DEFAULT 0.00,
        reorder_point INT             DEFAULT 10,
        unit          VARCHAR(30)     DEFAULT 'pcs',
        status        ENUM('active','inactive','discontinued') DEFAULT 'active',
        created_by    INT             NULL,
        created_at    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
        updated_at    TIMESTAMP       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_sku_tenant (sku, tenant_id),
        FOREIGN KEY (tenant_id)    REFERENCES tenants(id)    ON DELETE CASCADE,
        FOREIGN KEY (category_id)  REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL,
        FOREIGN KEY (created_by)   REFERENCES users(id)      ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS custom_fields (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id   INT          NOT NULL,
        entity_type VARCHAR(30)  DEFAULT 'item',
        label       VARCHAR(100) NOT NULL,
        field_key   VARCHAR(50)  NOT NULL,
        field_type  ENUM('text','number','date','select','checkbox','textarea','url','email') NOT NULL,
        options     JSON         NULL,
        required    TINYINT(1)   DEFAULT 0,
        sort_order  INT          DEFAULT 0,
        UNIQUE KEY uq_key_tenant_type (field_key, tenant_id, entity_type),
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS custom_field_values (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id       INT  NOT NULL,
        custom_field_id INT  NOT NULL,
        entity_id       INT  NOT NULL,
        value           TEXT NULL,
        UNIQUE KEY uq_field_entity (custom_field_id, entity_id),
        FOREIGN KEY (custom_field_id) REFERENCES custom_fields(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS stock_movements (
        id                 INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id          INT          NOT NULL,
        item_id            INT          NOT NULL,
        from_warehouse_id  INT          NULL,
        to_warehouse_id    INT          NULL,
        movement_type      ENUM('in','out','transfer','adjustment') NOT NULL,
        quantity           INT          NOT NULL,
        reference          VARCHAR(100) NULL,
        notes              TEXT         NULL,
        user_id            INT          NOT NULL,
        created_at         TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tenant_id)         REFERENCES tenants(id)    ON DELETE CASCADE,
        FOREIGN KEY (item_id)           REFERENCES items(id)      ON DELETE CASCADE,
        FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL,
        FOREIGN KEY (to_warehouse_id)   REFERENCES warehouses(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id)           REFERENCES users(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS activity_logs (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        tenant_id   INT          NOT NULL,
        user_id     INT          NULL,
        action      VARCHAR(100) NOT NULL,
        entity_type VARCHAR(50)  NULL,
        entity_id   INT          NULL,
        details     JSON         NULL,
        ip_address  VARCHAR(45)  NULL,
        created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

$results = [];
$failed  = false;

foreach ($statements as $sql) {
    preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $m);
    $table = $m[1] ?? 'unknown';
    try {
        $pdo->exec($sql);
        $results[] = ['table' => $table, 'ok' => true, 'msg' => 'Created'];
    } catch (PDOException $e) {
        $results[] = ['table' => $table, 'ok' => false, 'msg' => $e->getMessage()];
        $failed = true;
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
?>
<!DOCTYPE html>
<html>
<head><title>Schema Install</title><style>body{font-family:sans-serif;max-width:600px;margin:60px auto;padding:0 20px}code{background:#f3f4f6;padding:2px 6px;border-radius:4px}table{border-collapse:collapse;width:100%}td,th{padding:8px 12px;border:1px solid #e5e7eb;text-align:left}th{background:#f9fafb}.ok{color:#16a34a}.fail{color:#dc2626}</style></head>
<body>
<h2><?= $failed ? '❌ Schema install failed' : '✅ Schema installed' ?></h2>
<p>Database: <code><?= DB_NAME ?></code></p>
<table>
  <tr><th>Table</th><th>Status</th><th>Message</th></tr>
  <?php foreach ($results as $r): ?>
  <tr>
    <td><code><?= htmlspecialchars($r['table']) ?></code></td>
    <td class="<?= $r['ok'] ? 'ok' : 'fail' ?>"><?= $r['ok'] ? '✓ OK' : '✗ Failed' ?></td>
    <td><?= htmlspecialchars($r['msg']) ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<p style="margin-top:24px;color:#dc2626"><strong>⚠ Delete this file now:</strong> <code>sql/schema.php</code></p>
</body>
</html>
