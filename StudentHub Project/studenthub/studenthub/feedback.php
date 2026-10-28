<?php
$pageTitle = 'Feedback';
require_once 'includes/auth.php';
$depts = ['Computer Engineering', 'Information Technology', 'Electronics', 'Mechanical', 'Civil'];
$success = ''; $error = ''; $old = ['name' => '', 'email' => '', 'department' => '', 'feedback' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) { $old[$k] = trim($_POST[$k] ?? ''); }
    if (in_array('', $old, true)) { $error = 'All fields are required.'; }
    elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) { $error = 'Please enter a valid email address.'; }
    elseif (!in_array($old['department'], $depts, true)) { $error = 'Please choose a valid department.'; }
    elseif (strlen($old['feedback']) > 1000) { $error = 'Feedback is too long (max 1000 characters).'; }
    elseif (save_csv('feedback.csv', ['Name', 'Email', 'Department', 'Feedback', 'Date'], [$old['name'], $old['email'], $old['department'], $old['feedback'], date('Y-m-d H:i:s')])) {
        $success = 'Thank you for your feedback!'; $old = array_map(fn($v) => '', $old);
    } else { $error = 'Unable to save your feedback. Please try again.'; }
}
include 'includes/header.php';
?>
<h1>Feedback</h1>
<section class="card form-card">
    <?php if ($success): ?><p class="alert success"><?= e($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
    <form method="POST" action="">
        <div class="form-group"><label for="name">Full Name</label><input type="text" id="name" name="name" value="<?= e($old['name']) ?>" required></div>
        <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required></div>
        <div class="form-group"><label for="department">Department</label>
            <select id="department" name="department" required><option value="">-- Select --</option>
            <?php foreach ($depts as $d): ?><option <?= $old['department'] === $d ? 'selected' : '' ?>><?= e($d) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label for="feedback">Your Feedback</label><textarea id="feedback" name="feedback" rows="5" required><?= e($old['feedback']) ?></textarea></div>
        <button type="submit">Submit</button> <button type="reset" class="btn-outline">Clear</button>
    </form>
</section>
<?php include 'includes/footer.php'; ?>
