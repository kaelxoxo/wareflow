<?php
require_once __DIR__ . '/config/app.php';

$secret = $_GET['secret'] ?? '';
if ($secret !== 'wareflow_migrate_2024') {
    http_response_code(403);
    die('Forbidden. Add ?secret=wareflow_migrate_2024 to the URL.');
}

try {
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Verify which DB we're connected to
    $current = $pdo->query("SELECT DATABASE()")->fetchColumn();
    echo "<p>Connected to database: <strong>" . htmlspecialchars($current) . "</strong></p>";

    $sql = file_get_contents(__DIR__ . '/sql/schema.sql');
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => $s !== '' && !str_starts_with(ltrim($s), '--')
    );

    $results = [];
    foreach ($statements as $stmt) {
        try {
            $pdo->exec($stmt);
            $results[] = "<li style='color:green'>✓ " . htmlspecialchars(substr(trim($stmt), 0, 80)) . "</li>";
        } catch (PDOException $e) {
            $results[] = "<li style='color:orange'>⚠ " . htmlspecialchars(substr(trim($stmt), 0, 80)) . " — " . htmlspecialchars($e->getMessage()) . "</li>";
        }
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tables in DB: <strong>" . implode(', ', $tables) . "</strong></p>";
    echo "<ul>" . implode('', $results) . "</ul>";
    echo "<p><strong>Delete migrate.php now.</strong></p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Error</h2><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
