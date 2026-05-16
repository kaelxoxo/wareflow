<?php
if (($_GET['secret'] ?? '') !== 'stripe_cols_2025') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/config/app.php';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("ALTER TABLE tenants
        ADD COLUMN IF NOT EXISTS stripe_customer_id      VARCHAR(100) NULL AFTER plan,
        ADD COLUMN IF NOT EXISTS stripe_subscription_id  VARCHAR(100) NULL AFTER stripe_customer_id,
        ADD COLUMN IF NOT EXISTS subscription_status     ENUM('none','trialing','active','past_due','canceled','unpaid') NOT NULL DEFAULT 'none' AFTER stripe_subscription_id,
        ADD COLUMN IF NOT EXISTS subscription_period_end TIMESTAMP NULL AFTER subscription_status");
    echo '<pre>Done. Columns added (or already existed).</pre>';
} catch (Exception $e) {
    echo '<pre>Error: ' . htmlspecialchars($e->getMessage()) . '</pre>';
}
