<?php
// ONE-TIME migration script — DELETE THIS FILE after running it.
require_once __DIR__ . '/config/app.php';

$secret = $_GET['secret'] ?? '';
if ($secret !== 'wareflow_migrate_2024') {
    http_response_code(403);
    die('Forbidden. Add ?secret=wareflow_migrate_2024 to the URL.');
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create DB if not exists, then select it
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");

    $sql = file_get_contents(__DIR__ . '/sql/schema.sql');

    // Split into individual statements
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => $s !== '' && !str_starts_with(ltrim($s), '--')
    );

    $results = [];
    foreach ($statements as $stmt) {
        try {
            $pdo->exec($stmt);
            $first = substr(trim($stmt), 0, 60);
            $results[] = "<li style='color:green'>✓ " . htmlspecialchars($first) . "...</li>";
        } catch (PDOException $e) {
            $first = substr(trim($stmt), 0, 60);
            $results[] = "<li style='color:orange'>⚠ " . htmlspecialchars($first) . " — " . htmlspecialchars($e->getMessage()) . "</li>";
        }
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "<h2>Migration complete</h2><ul>" . implode('', $results) . "</ul>";
    echo "<p><strong>Delete migrate.php from your repo now.</strong></p>";

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Connection failed</h2><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
