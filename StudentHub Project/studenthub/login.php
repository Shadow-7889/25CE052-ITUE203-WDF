<?php
// Practical 10: secure login with sessions, password_verify, session id regeneration, role-based redirect
$pageTitle = 'Login';
require_once 'includes/auth.php';
if (is_logged_in()) { redirect(is_admin() ? 'admin/index.php' : 'dashboard.php'); }
$error = ''; $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare('SELECT user_id, name, email, password_hash, role, status FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
        } elseif ($user['status'] !== 'active') {
            $error = 'Your account is inactive. Please contact the administrator.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            $u = $conn->prepare('UPDATE users SET last_login = NOW() WHERE user_id = ?');
            $u->bind_param('i', $_SESSION['user_id']); $u->execute(); $u->close();
            audit($conn, 'login', 'User logged in');
            redirect($user['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php');
        }
    }
}
include 'includes/header.php';
?>
<section class="card form-card">
    <h1>Login</h1>
    <?php if ($error): ?><p class="alert error" role="alert"><?= e($error) ?></p><?php endif; ?>
    <form method="POST" action="">
        <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus></div>
        <div class="form-group"><label for="password">Password</label><input type="password" id="password" name="password" autocomplete="current-password" required></div>
        <button type="submit">Login</button>
        <p class="muted">New here? <a href="<?= e(url('register.php')) ?>">Create an account</a></p>
    </form>
    <p class="alert info"><strong>Demo accounts</strong><br>Admin: admin@studenthub.local / Admin@123<br>Student: student@studenthub.local / Student@123</p>
</section>
<?php include 'includes/footer.php'; ?>
