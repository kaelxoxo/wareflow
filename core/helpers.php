<?php
function redirect(string $path, int $code = 302): never {
    header('Location: ' . BASE_URL . $path, true, $code);
    exit();
}

function view(string $tpl, array $data = [], string $layout = 'app'): void {
    extract($data, EXTR_SKIP);
    $tplPath = ROOT_DIR . '/views/' . $tpl . '.php';
    if (!file_exists($tplPath)) {
        http_response_code(404);
        echo "<h1>View not found: {$tpl}</h1>";
        return;
    }
    ob_start();
    include $tplPath;
    $content = ob_get_clean();

    if ($layout) {
        $layoutPath = ROOT_DIR . '/views/layouts/' . $layout . '.php';
        include $layoutPath;
    } else {
        echo $content;
    }
}

function json_response(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash(string $type, string $msg): void {
    Auth::start();
    $_SESSION['_flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    Auth::start();
    $f = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $f;
}

function old(string $key, string $default = ''): string {
    return e($_SESSION['_old'][$key] ?? $default);
}

function set_old(array $data): void {
    $_SESSION['_old'] = $data;
}

function clear_old(): void {
    unset($_SESSION['_old']);
}

function asset(string $path): string {
    return BASE_URL . '/public/' . ltrim($path, '/');
}

function url(string $path): string {
    return BASE_URL . $path;
}

function is_active(string ...$paths): string {
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base    = BASE_PATH;
    $rel     = '/' . ltrim(substr($current, strlen($base)), '/');
    foreach ($paths as $p) {
        if ($rel === $p || ($p !== '/' && str_starts_with($rel, $p))) {
            return 'active';
        }
    }
    return '';
}

function money(float $v): string {
    return '$' . number_format($v, 2);
}

function ago(string $dt): string {
    $diff = time() - strtotime($dt);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . 'm ago';
    if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('M j, Y', strtotime($dt));
}

function status_badge(string $status): string {
    $map = [
        'active'       => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
        'inactive'     => 'bg-surface-container-low text-on-surface-variant dark:bg-gray-800 dark:text-gray-400',
        'discontinued' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
        'invited'      => 'bg-[#eff4ff] text-primary dark:bg-blue-900/30 dark:text-blue-400',
        'suspended'    => 'bg-orange-50 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
        'in'           => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
        'out'          => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400',
        'transfer'     => 'bg-[#eff6ff] text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
        'adjustment'   => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400',
    ];
    $cls = $map[$status] ?? 'bg-surface-container-low text-on-surface-variant';
    return "<span class=\"badge {$cls}\">" . ucfirst(e($status)) . "</span>";
}

function csrf_field(): string {
    return '<input type="hidden" name="_token" value="' . Auth::csrf() . '">';
}

function paginate_links(array $pagination): string {
    if ($pagination['last_page'] <= 1) return '';
    $current = $pagination['current_page'];
    $last    = $pagination['last_page'];
    $base    = strtok($_SERVER['REQUEST_URI'], '?');
    $params  = $_GET;
    $html    = '<div class="flex items-center gap-1">';
    for ($i = 1; $i <= $last; $i++) {
        $params['page'] = $i;
        $qs  = http_build_query($params);
        $url = $base . '?' . $qs;
        $cls = $i === $current
            ? 'px-3 py-1 bg-primary text-on-primary rounded-md text-[13px] font-semibold'
            : 'px-3 py-1 bg-surface-container text-on-surface-variant rounded-md text-[13px] hover:bg-surface-container-high transition-colors';
        $html .= "<a href=\"{$url}\" class=\"{$cls}\">{$i}</a>";
    }
    $html .= '</div>';
    return $html;
}
