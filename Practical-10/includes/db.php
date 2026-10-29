<?php
$__root = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$__doc  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$__path = substr($__root, strlen($__doc));
define('BASE_URL', implode('/', array_map('rawurlencode', explode('/', $__path))) . '/');
mysqli_report(MYSQLI_REPORT_OFF);          
$conn = new mysqli('localhost', 'root', '', 'studenthub');
if ($conn->connect_error) {
    die('Database connection failed. Please check includes/db.php and that MySQL is running.');
}
$conn->set_charset('utf8mb4');

const PRACTICAL = 'Practical 10';
const HOME_ADMIN = 'admin.php';
const HOME_STUDENT = 'dashboard.php';
const NAV = [
    ['Dashboard', 'dashboard.php', 'student'],
    ['Admin Panel', 'admin.php', 'admin'],
    ['Login', 'login.php', 'guest'],
    ['Logout', 'logout.php', 'user'],
];
