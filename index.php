<?php
declare(strict_types=1);

$dev = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost';
ini_set('display_errors', $dev ? '1' : '0');
error_reporting(E_ALL);

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/stripe.php';
require_once __DIR__ . '/core/DB.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/helpers.php';

// Models
require_once __DIR__ . '/models/Tenant.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Warehouse.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Item.php';
require_once __DIR__ . '/models/CustomField.php';
require_once __DIR__ . '/models/StockMovement.php';
require_once __DIR__ . '/models/ActivityLog.php';

// Controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/InventoryController.php';
require_once __DIR__ . '/controllers/WarehouseController.php';
require_once __DIR__ . '/controllers/StockController.php';
require_once __DIR__ . '/controllers/CustomFieldController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/SettingsController.php';
require_once __DIR__ . '/controllers/ApiController.php';
require_once __DIR__ . '/controllers/BillingController.php';

Auth::start();

// Resolve URI relative to app base path
$rawUri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$base    = rtrim(BASE_PATH, '/');
$path    = '/' . ltrim(substr($rawUri, strlen($base)), '/');
$method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// ── Route Dispatch ────────────────────────────────────────────────────────────
$matched = false;

function route(string $m, string $pattern, callable $handler): bool {
    global $method, $path, $matched;
    if ($matched) return true;
    $regex = '#^' . preg_replace(['/\{id\}/', '/\{\w+\}/'], ['(\d+)', '([^/]+)'], $pattern) . '$#';
    if ($method === $m && preg_match($regex, $path, $ms)) {
        $matched = true;
        array_shift($ms);
        call_user_func_array($handler, $ms);
    }
    return $matched;
}

// Public routes
route('GET',  '/',             fn() => (Auth::check() ? redirect('/dashboard') : view('landing', [], 'landing')));
route('GET',  '/login',        fn() => (new AuthController)->loginForm());
route('POST', '/login',        fn() => (new AuthController)->login());
route('GET',  '/register',     fn() => (new AuthController)->registerForm());
route('POST', '/register',     fn() => (new AuthController)->register());
route('GET',  '/logout',       fn() => (new AuthController)->logout());
route('GET',  '/invite/{token}',  fn($t) => (new AuthController)->inviteForm($t));
route('POST', '/invite/accept',fn() => (new AuthController)->inviteAccept());

// Dashboard
route('GET', '/dashboard', fn() => (new DashboardController)->index());

// Inventory
route('GET',  '/inventory',              fn() => (new InventoryController)->index());
route('GET',  '/inventory/create',       fn() => (new InventoryController)->createForm());
route('POST', '/inventory',              fn() => (new InventoryController)->create());
route('GET',  '/inventory/{id}/edit',    fn($id) => (new InventoryController)->editForm((int)$id));
route('POST', '/inventory/{id}/update',  fn($id) => (new InventoryController)->update((int)$id));
route('POST', '/inventory/{id}/delete',  fn($id) => (new InventoryController)->delete((int)$id));

// Warehouses
route('GET',  '/warehouses',             fn() => (new WarehouseController)->index());
route('POST', '/warehouses',             fn() => (new WarehouseController)->create());
route('POST', '/warehouses/{id}/update', fn($id) => (new WarehouseController)->update((int)$id));
route('POST', '/warehouses/{id}/delete', fn($id) => (new WarehouseController)->delete((int)$id));

// Stock movements
route('GET',  '/stock',         fn() => (new StockController)->index());
route('POST', '/stock/transfer', fn() => (new StockController)->transfer());

// Custom fields
route('GET',  '/custom-fields',             fn() => (new CustomFieldController)->index());
route('POST', '/custom-fields',             fn() => (new CustomFieldController)->create());
route('POST', '/custom-fields/{id}/delete', fn($id) => (new CustomFieldController)->delete((int)$id));

// Users
route('GET',  '/users',                fn() => (new UserController)->index());
route('POST', '/users/invite',         fn() => (new UserController)->invite());
route('POST', '/users/{id}/role',      fn($id) => (new UserController)->assignRole((int)$id));
route('POST', '/users/{id}/suspend',   fn($id) => (new UserController)->suspend((int)$id));
route('POST', '/users/{id}/delete',    fn($id) => (new UserController)->delete((int)$id));

// Settings
route('GET',  '/settings',                fn() => (new SettingsController)->index());
route('POST', '/settings/tenant',         fn() => (new SettingsController)->updateTenant());
route('POST', '/settings/profile',        fn() => (new SettingsController)->updateProfile());
route('POST', '/settings/password',       fn() => (new SettingsController)->updatePassword());
route('POST', '/settings/categories',     fn() => (new SettingsController)->createCategory());
route('POST', '/settings/categories/{id}/delete', fn($id) => (new SettingsController)->deleteCategory((int)$id));

// Billing — temporarily disabled
// route('GET',  '/billing',          fn() => (new BillingController)->index());
// route('POST', '/billing/checkout', fn() => (new BillingController)->checkout());
// route('GET',  '/billing/success',  fn() => (new BillingController)->success());
// route('GET',  '/billing/cancel',   fn() => (new BillingController)->cancel());
// route('POST', '/billing/portal',   fn() => (new BillingController)->portal());
route('POST', '/billing/webhook',  fn() => (new BillingController)->webhook());

// API routes
route('GET', '/api/dashboard/kpis',    fn() => (new ApiController)->kpis());
route('GET', '/api/items',             fn() => (new ApiController)->items());
route('GET', '/api/activity-logs',     fn() => (new ApiController)->activityLogs());
route('GET', '/api/stock/trend',       fn() => (new ApiController)->stockTrend());

// 404
if (!$matched) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><body style="font-family:sans-serif;text-align:center;padding:4rem">
    <h1 style="color:#004ac6">404</h1><p>Page not found.</p>
    <a href="' . BASE_URL . '/" style="color:#004ac6">← Back home</a></body></html>';
}
