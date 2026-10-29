<?php
$pageTitle = 'Student Dashboard';
require_once __DIR__ . '/includes/auth.php';
require_login();
if (is_admin()) { redirect(HOME_ADMIN); }
$uid = $_SESSION['user_id'];
$st = $conn->prepare('SELECT name, email, role, last_login FROM users WHERE user_id = ?');
$st->bind_param('i', $uid); $st->execute();
$me = $st->get_result()->fetch_assoc(); $st->close();
include __DIR__ . '/includes/header.php';
?>
<h1>Student Dashboard</h1>
<section class="card form-card">
    <table>
        <tr><th>Name</th><td><?= e($me['name']) ?></td></tr>
        <tr><th>Email</th><td><?= e($me['email']) ?></td></tr>
        <tr><th>Role</th><td><?= e($me['role']) ?></td></tr>
        <tr><th>Last login</th><td><?= e($me['last_login'] ?? 'first login') ?></td></tr>
        <tr><th>Session timeout</th><td><?= SESSION_TIMEOUT / 60 ?> minutes of inactivity</td></tr>
        <tr><th>Session ID</th><td><?= e(substr(session_id(), 0, 10)) ?>...</td></tr>
    </table>
    <p><a class="btn btn-outline" href="<?= e(url('admin.php')) ?>">Try opening the Admin Panel</a> <a class="btn" href="<?= e(url('logout.php')) ?>">Logout</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
