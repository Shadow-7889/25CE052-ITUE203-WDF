<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'StudentHub';
$here = ltrim(substr($_SERVER['SCRIPT_NAME'], strlen(rawurldecode(BASE_URL))), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> - <?= e(PRACTICAL) ?></title>
<link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
<script>try{if(localStorage.getItem('theme')==='dark')document.documentElement.classList.add('dark');}catch(e){}</script>
</head>
<body>
<header class="site-header">
    <a href="<?= e(url('index.php')) ?>" class="logo">StudentHub <small>&middot; <?= e(PRACTICAL) ?></small></a>
    <button type="button" class="hamburger" id="hamburger" aria-label="Toggle navigation" aria-expanded="false">&#9776;</button>
    <nav class="nav-links" id="navLinks" aria-label="Main navigation">
<?php foreach (NAV as [$label, $file, $access]):
    $ok = $access === 'all' || ($access === 'guest' && !is_logged_in()) || ($access === 'user' && is_logged_in())
        || ($access === 'admin' && is_admin()) || ($access === 'student' && is_logged_in() && !is_admin());
    if (!$ok) continue; ?>
        <a href="<?= e(url($file)) ?>"<?= $here === $file ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
<?php endforeach; ?>
        <button type="button" id="themeToggle" class="theme-btn">&#127769; Dark</button>
    </nav>
</header>
<main class="container" id="main">
<?php show_flash(); ?>
