<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'StudentHub';
$here = ltrim(substr($_SERVER['SCRIPT_NAME'], strlen(rawurldecode(BASE_URL))), '/');
if (!function_exists('nav_link')) {
    function nav_link($file, $label, $here) {
        $cur = $here === $file ? ' aria-current="page"' : '';
        return '<a href="' . e(url($file)) . '"' . $cur . '>' . e($label) . '</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> - StudentHub</title>
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<script>try{if(localStorage.getItem('theme')==='dark')document.documentElement.classList.add('dark');}catch(e){}</script>
</head>
<body>
<?php if (!empty($showBanner)): ?>
<div class="notification" id="notification" role="status">
    <span>Welcome to StudentHub! Explore events, register and manage your student life.</span>
    <button type="button" id="closeNotification" aria-label="Close notification">&times;</button>
</div>
<?php endif; ?>
<header class="site-header">
    <a href="<?= e(url('index.php')) ?>" class="logo">StudentHub</a>
    <button type="button" class="hamburger" id="hamburger" aria-label="Toggle navigation" aria-expanded="false">&#9776;</button>
    <nav class="nav-links" id="navLinks" aria-label="Main navigation">
        <?= nav_link('index.php', 'Home', $here) ?>
        <?= nav_link('about.php', 'About', $here) ?>
        <?= nav_link('events.php', 'Events', $here) ?>
        <?= nav_link('explorer.php', 'Data Explorer', $here) ?>
        <?= nav_link('faq.php', 'FAQ', $here) ?>
        <?= nav_link('contact.php', 'Contact', $here) ?>
        <?= nav_link('feedback.php', 'Feedback', $here) ?>
        <?php if (!is_logged_in()): ?>
            <?= nav_link('login.php', 'Login', $here) ?>
            <?= nav_link('register.php', 'Register', $here) ?>
        <?php else: ?>
            <?php if (is_admin()): ?>
                <?php if (file_exists(__DIR__ . '/../admin/index.php')) echo nav_link('admin/index.php', 'Admin Panel', $here); ?>
                <?php if (file_exists(__DIR__ . '/../admin/students.php')) echo nav_link('admin/students.php', 'Students', $here); ?>
                <?php if (file_exists(__DIR__ . '/../admin/events.php')) echo nav_link('admin/events.php', 'Manage Events', $here); ?>
            <?php else: ?>
                <?= nav_link('dashboard.php', 'Dashboard', $here) ?>
                <?= nav_link('profile.php', 'Profile', $here) ?>
            <?php endif; ?>
            <a href="<?= e(url('logout.php')) ?>">Logout (<?= e($_SESSION['name'] ?? '') ?>)</a>
        <?php endif; ?>
        <button type="button" id="themeToggle" class="theme-btn">&#127769; Dark</button>
    </nav>
</header>
<main class="container" id="main">
<?php show_flash(); ?>
