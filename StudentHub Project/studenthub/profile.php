<?php
$pageTitle = 'Profile';
require_once 'includes/auth.php';
require_login();
$uid = $_SESSION['user_id'];
$st = $conn->prepare('SELECT u.name, u.email, u.mobile, u.gender, u.role, u.created_at, s.course, s.year FROM users u LEFT JOIN students s ON s.user_id = u.user_id WHERE u.user_id = ?');
$st->bind_param('i', $uid); $st->execute();
$me = $st->get_result()->fetch_assoc(); $st->close();
include 'includes/header.php';
?>
<h1>My Profile</h1>
<section class="card form-card">
    <table>
        <tr><th>Name</th><td><?= e($me['name']) ?></td></tr>
        <tr><th>Email</th><td><?= e($me['email']) ?></td></tr>
        <tr><th>Mobile</th><td><?= e($me['mobile'] ?? '-') ?></td></tr>
        <tr><th>Gender</th><td><?= e($me['gender'] ?? '-') ?></td></tr>
        <tr><th>Role</th><td><?= e(ucfirst($me['role'])) ?></td></tr>
        <tr><th>Course</th><td><?= e($me['course'] ?? '-') ?></td></tr>
        <tr><th>Year</th><td><?= e($me['year'] ?? '-') ?></td></tr>
        <tr><th>Department</th><td>U &amp; PU Patel Department of Computer Engineering</td></tr>
        <tr><th>Member since</th><td><?= e($me['created_at']) ?></td></tr>
    </table>
</section>
<?php include 'includes/footer.php'; ?>
