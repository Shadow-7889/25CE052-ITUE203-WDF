<?php
$pageTitle = 'Dashboard';
require_once 'includes/auth.php';
require_login();
if (is_admin()) { redirect('admin/index.php'); }
$uid = $_SESSION['user_id'];
$st = $conn->prepare('SELECT u.name, u.email, u.last_login, s.student_id, s.course, s.year FROM users u LEFT JOIN students s ON s.user_id = u.user_id WHERE u.user_id = ?');
$st->bind_param('i', $uid); $st->execute();
$me = $st->get_result()->fetch_assoc(); $st->close();
$regs = null;
if (!empty($me['student_id'])) {
    $r = $conn->prepare('SELECT e.event_name, e.event_date, e.venue, r.registration_date FROM registrations r JOIN events e ON e.event_id = r.event_id WHERE r.student_id = ? ORDER BY e.event_date');
    $r->bind_param('i', $me['student_id']); $r->execute();
    $regs = $r->get_result(); 
}
include 'includes/header.php';
?>
<h1>Welcome, <?= e($me['name']) ?></h1>
<div class="stats">
    <div class="card stat"><b><?= e($me['course'] ?? '-') ?></b>Course</div>
    <div class="card stat"><b><?= e($me['year'] ?? '-') ?></b>Year</div>
    <div class="card stat"><b><?= $regs ? $regs->num_rows : 0 ?></b>Events registered</div>
</div>
<h2 class="section-title">Quick Links</h2>
<div class="grid">
    <a class="card" href="<?= e(url('profile.php')) ?>">Profile</a>
    <a class="card" href="<?= e(url('events.php')) ?>">Events</a>
    <a class="card" href="<?= e(url('attendance.php')) ?>">Attendance</a>
    <a class="card" href="<?= e(url('assignments.php')) ?>">Assignments</a>
    <a class="card" href="<?= e(url('results.php')) ?>">Results</a>
    <a class="card" href="<?= e(url('timetable.php')) ?>">Timetable</a>
    <a class="card" href="<?= e(url('notices.php')) ?>">Notices</a>
    <a class="card" href="<?= e(url('feedback.php')) ?>">Feedback</a>
</div>
<h2 class="section-title">My Event Registrations</h2>
<div class="table-wrap"><table><tr><th>Event</th><th>Date</th><th>Venue</th><th>Registered On</th></tr>
<?php if ($regs && $regs->num_rows): while ($row = $regs->fetch_assoc()): ?>
<tr><td><?= e($row['event_name']) ?></td><td><?= e($row['event_date']) ?></td><td><?= e($row['venue']) ?></td><td><?= e($row['registration_date']) ?></td></tr>
<?php endwhile; else: ?><tr><td colspan="4">You have not registered for any event yet.</td></tr><?php endif; ?>
</table></div>
<p class="muted">Last login: <?= e($me['last_login'] ?? 'first login') ?></p>
<?php include 'includes/footer.php'; ?>
