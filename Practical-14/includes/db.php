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

const PRACTICAL = 'Practical 14';
const HOME_ADMIN = 'index.php';
const HOME_STUDENT = 'login.php';
const NAV = [
    ['Dashboard', 'index.php', 'admin'],
    ['Students', 'students.php', 'admin'],
    ['Events', 'events.php', 'admin'],
    ['Login', 'login.php', 'guest'],
    ['Logout', 'logout.php', 'user'],
];
