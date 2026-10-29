<?php
$pageTitle = 'Admin Panel';
require_once __DIR__ . '/includes/auth.php';
require_role('admin');
$users = $conn->query('SELECT name, email, role, status, last_login FROM users ORDER BY user_id');
include __DIR__ . '/includes/header.php';
?>
<h1>Admin Panel</h1>
<p class="alert success">Welcome, <?= e($_SESSION['name']) ?>. You are logged in as <strong>admin</strong>.</p>
<div class="table-wrap"><table>
<tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th></tr>
<?php while ($u = $users->fetch_assoc()): ?>
<tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td><td><?= e($u['status']) ?></td><td><?= e($u['last_login'] ?? '-') ?></td></tr>
<?php endwhile; ?>
</table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
