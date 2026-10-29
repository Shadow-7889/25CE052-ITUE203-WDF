<?php

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/includes/auth.php';
require_role('admin');
$queries = [
    'Total users' => 'SELECT COUNT(*) FROM users',
    'Active users' => "SELECT COUNT(*) FROM users WHERE status = 'active'",
    'Students' => 'SELECT COUNT(*) FROM students',
    'Events' => 'SELECT COUNT(*) FROM events',
    'Event registrations' => 'SELECT COUNT(*) FROM registrations',
];
$stats = [];
foreach ($queries as $label => $sql) { $res = $conn->query($sql); $stats[$label] = $res ? (int)$res->fetch_row()[0] : 0; }
$newUsers = $conn->query('SELECT name, email, role, created_at FROM users ORDER BY user_id DESC LIMIT 5');
$latest = $conn->query('SELECT s.name, e.event_name, r.registration_date FROM registrations r JOIN students s ON s.student_id = r.student_id JOIN events e ON e.event_id = r.event_id ORDER BY r.registration_id DESC LIMIT 5');
$logs = $conn->query('SELECT a.action, a.details, a.created_at, u.name FROM audit_logs a LEFT JOIN users u ON u.user_id = a.user_id ORDER BY a.log_id DESC LIMIT 10');
include __DIR__ . '/includes/header.php';
?>
<h1>Admin Dashboard</h1>
<div class="stats">
<?php foreach ($stats as $label => $n): ?><div class="card stat"><b><?= $n ?></b><?= e($label) ?></div><?php endforeach; ?>
</div>
<h2 class="section-title">Management</h2>
<div class="grid">
    <a class="card" href="<?= e(url('students.php')) ?>">Manage Students (CRUD)</a>
    <a class="card" href="<?= e(url('events.php')) ?>">Manage Events and Posters</a>
</div>
<div class="two-col" style="margin-top:24px">
<section><h2>Latest Registered Users</h2><div class="table-wrap"><table><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>
<?php while ($r = $newUsers->fetch_assoc()): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['role']) ?></td><td><?= e($r['created_at']) ?></td></tr><?php endwhile; ?></table></div></section>
<section><h2>Latest Event Registrations</h2><div class="table-wrap"><table><tr><th>Student</th><th>Event</th><th>Date</th></tr>
<?php if ($latest->num_rows): while ($r = $latest->fetch_assoc()): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['event_name']) ?></td><td><?= e($r['registration_date']) ?></td></tr><?php endwhile; else: ?><tr><td colspan="3">No registrations yet.</td></tr><?php endif; ?></table></div></section>
</div>
<h2 class="section-title">Audit Log (last 10 actions)</h2>
<div class="table-wrap"><table><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th></tr>
<?php while ($r = $logs->fetch_assoc()): ?><tr><td><?= e($r['created_at']) ?></td><td><?= e($r['name'] ?? 'system') ?></td><td><?= e($r['action']) ?></td><td><?= e($r['details']) ?></td></tr><?php endwhile; ?></table></div>
<?php include __DIR__ . '/includes/footer.php'; ?>
