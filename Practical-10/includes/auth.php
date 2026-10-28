<?php
require_once __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
const SESSION_TIMEOUT = 1800; // 30 minutes of inactivity
if (isset($_SESSION['user_id'])) {
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT) {
        $_SESSION = [];
        session_destroy();
        session_start();
        $_SESSION['flash'] = ['error', 'Your session expired due to inactivity. Please log in again.'];
    } else {
        $_SESSION['last_activity'] = time();
    }
}
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function url($p = '') { return BASE_URL . ltrim($p, '/'); }
function redirect($p) { header('Location: ' . url($p)); exit; }
function is_logged_in() { return isset($_SESSION['user_id']); }
function is_admin() { return ($_SESSION['role'] ?? '') === 'admin'; }
function flash($type, $msg) { $_SESSION['flash'] = [$type, $msg]; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$t, $m] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<p class="alert ' . e($t) . '" role="alert">' . e($m) . '</p>';
    }
}
function require_login() {
    if (!is_logged_in()) { flash('error', 'Please log in to access that page.'); redirect('login.php'); }
}
function forbidden() {
    http_response_code(403);
    $pageTitle = 'Access Denied';
    include __DIR__ . '/header.php';
    echo '<section class="card"><h1>403 - Access Denied</h1><p>You do not have permission to open this page.</p><p><a class="btn" href="' . e(url('index.php')) . '">Back to Home</a></p></section>';
    include __DIR__ . '/footer.php';
    exit;
}
function require_role($role) {
    require_login();
    if (($_SESSION['role'] ?? '') !== $role) { forbidden(); }
}
function audit($conn, $action, $details = '') {
    $uid = $_SESSION['user_id'] ?? null;
    $s = $conn->prepare('INSERT INTO audit_logs (user_id, action, details) VALUES (?, ?, ?)');
    if ($s) { $s->bind_param('iss', $uid, $action, $details); $s->execute(); $s->close(); }
}
// Append one row to a CSV file safely (Practical 7). Header is written when the file is new.
function save_csv($file, array $header, array $row) {
    $path = __DIR__ . '/../storage/' . $file;
    $row = array_map(fn($v) => preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v, $row); // CSV-injection guard
    $fh = fopen($path, 'a');
    if ($fh === false) return false;
    flock($fh, LOCK_EX);
    clearstatcache(true, $path);
    if (filesize($path) === 0) fputcsv($fh, $header);
    fputcsv($fh, $row);
    flock($fh, LOCK_UN);
    fclose($fh);
    return true;
}
