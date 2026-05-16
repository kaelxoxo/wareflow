<?php
// Detect base URL dynamically
$scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = $_SERVER['SCRIPT_NAME'] ?? '/warehouse_inventory_management/index.php';
$basePath = rtrim(dirname($script), '/');

define('BASE_URL',  $scheme . '://' . $host . $basePath);
define('BASE_PATH', $basePath);
define('ROOT_DIR',  dirname(__DIR__));

define('APP_NAME',    'Wareflow');
define('APP_VERSION', '1.0.0');

// Database — reads .env values or falls back to XAMPP defaults
define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
define('DB_PORT',    getenv('DB_PORT')    ?: '3306');
define('DB_NAME',    getenv('DB_NAME')    ?: 'wareflow');
define('DB_USER',    getenv('DB_USER')    ?: 'root');
define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

// Session
define('SESSION_LIFETIME', 86400);
define('SESSION_NAME',     'wf_sess');

// Pagination
define('PER_PAGE', 20);
