<?php
// Practical 9: secure registration (mysqli, duplicate check, password_hash, server-side validation)
$pageTitle = 'Register'; $extraJs = ['validation.js'];
require_once 'includes/auth.php';
$courses = ['B.Tech', 'BCA', 'B.Sc IT', 'MCA'];
$v = ['name' => '', 'email' => '', 'mobile' => '', 'course' => '', 'year' => '', 'gender' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) { $v[$k] = trim($_POST[$k] ?? ''); }
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!preg_match("/^[A-Za-z][A-Za-z .'-]{1,59}$/", $v['name'])) $errors['name'] = 'Enter a valid full name (letters only, 2-60 characters).';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL) || strlen($v['email']) > 100) $errors['email'] = 'Enter a valid email address.';
    if (!preg_match('/^[0-9]{10}$/', $v['mobile'])) $errors['mobile'] = 'Mobile number must contain exactly 10 digits.';
    if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) $errors['password'] = 'Use 8+ characters with an uppercase letter, a lowercase letter and a digit.';
    if ($password !== $confirm) $errors['confirm_password'] = 'Passwords do not match.';
    if (!in_array($v['course'], $courses, true)) $errors['course'] = 'Please select your course.';
    if (!preg_match('/^[1-4]$/', $v['year'])) $errors['year'] = 'Year must be between 1 and 4.';
    if (!in_array($v['gender'], ['Male', 'Female', 'Other'], true)) $errors['gender'] = 'Please select your gender.';
    if (empty($_POST['terms'])) $errors['terms'] = 'You must accept the terms and conditions.';

    if (!$errors) {   // duplicate email check
        $chk = $conn->prepare('SELECT user_id FROM users WHERE email = ? UNION SELECT student_id FROM students WHERE email = ?');
        $chk->bind_param('ss', $v['email'], $v['email']);
        $chk->execute(); $chk->store_result();
        if ($chk->num_rows > 0) $errors['email'] = 'This email is already registered.';
        $chk->close();
    }
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $year = (int)$v['year'];
        $conn->begin_transaction();
        $ok = false;
        $s1 = $conn->prepare("INSERT INTO users (name, email, mobile, gender, password_hash, role) VALUES (?, ?, ?, ?, ?, 'student')");
        $s1->bind_param('sssss', $v['name'], $v['email'], $v['mobile'], $v['gender'], $hash);
        if ($s1->execute()) {
            $uid = $conn->insert_id;
            $s2 = $conn->prepare('INSERT INTO students (user_id, name, email, course, year) VALUES (?, ?, ?, ?, ?)');
            $s2->bind_param('isssi', $uid, $v['name'], $v['email'], $v['course'], $year);
            $ok = $s2->execute();
            $s2->close();
        } elseif ($s1->errno === 1062) {
            $errors['email'] = 'This email is already registered.';
        }
        $s1->close();
        if ($ok) {
            $conn->commit();
            flash('success', 'Registration successful! Your account has been created.');
            redirect('register.php');
        }
        $conn->rollback();
        if (!$errors) $errors['form'] = 'Registration failed. Please try again.';
    }
}
include 'includes/header.php';
function err($errors, $k) { return e($errors[$k] ?? ''); }
?>
<section class="card form-card">
    <h1>Student Registration</h1>
    <?php if (!empty($errors['form'])): ?><p class="alert error"><?= e($errors['form']) ?></p><?php endif; ?>
    <form id="registrationForm" method="POST" action="" novalidate>
        <div class="form-group"><label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= e($v['name']) ?>" autocomplete="name" required>
            <span class="field-error" id="nameError"><?= err($errors, 'name') ?></span></div>
        <div class="form-group"><label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= e($v['email']) ?>" autocomplete="email" required>
            <span class="field-error" id="emailError"><?= err($errors, 'email') ?></span></div>
        <div class="form-group"><label for="mobile">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" value="<?= e($v['mobile']) ?>" maxlength="10" inputmode="numeric" required>
            <span class="field-error" id="mobileError"><?= err($errors, 'mobile') ?></span></div>
        <div class="form-group"><label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>
            <div class="password-meter-track"><div id="strengthFill"></div></div><small id="strengthText" class="muted"></small>
            <span class="field-error" id="passwordError"><?= err($errors, 'password') ?></span></div>
        <div class="form-group"><label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" required>
            <span class="field-error" id="confirm_passwordError"><?= err($errors, 'confirm_password') ?></span></div>
        <div class="form-group"><label for="course">Course</label>
            <select id="course" name="course" required><option value="">-- Select Course --</option>
            <?php foreach ($courses as $c): ?><option <?= $v['course'] === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select>
            <span class="field-error" id="courseError"><?= err($errors, 'course') ?></span></div>
        <div class="form-group"><label for="year">Year of Study (1-4)</label>
            <input type="number" id="year" name="year" min="1" max="4" value="<?= e($v['year']) ?>" required>
            <span class="field-error" id="yearError"><?= err($errors, 'year') ?></span></div>
        <fieldset class="form-group inline-options" style="border:0;padding:0"><legend>Gender</legend>
            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
            <label><input type="radio" name="gender" value="<?= $g ?>" <?= $v['gender'] === $g ? 'checked' : '' ?>> <?= $g ?></label>
            <?php endforeach; ?>
            <span class="field-error" id="genderError"><?= err($errors, 'gender') ?></span></fieldset>
        <div class="form-group"><label><input type="checkbox" name="terms" id="terms" value="1" <?= !empty($_POST['terms']) ? 'checked' : '' ?>> I accept the terms and conditions</label>
            <span class="field-error" id="termsError"><?= err($errors, 'terms') ?></span></div>
        <button type="submit">Register</button> <button type="reset" class="btn-outline">Reset</button>
    </form>
</section>
<?php include 'includes/footer.php'; ?>
