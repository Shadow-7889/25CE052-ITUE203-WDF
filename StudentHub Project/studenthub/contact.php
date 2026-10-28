<?php
// Practical 7: PHP form processing, server-side validation and CSV storage
$pageTitle = 'Contact';
require_once 'includes/auth.php';
$success = ''; $error = ''; $old = ['name' => '', 'email' => '', 'mobile' => '', 'message' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $_) { $old[$k] = trim($_POST[$k] ?? ''); }
    if (in_array('', $old, true)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!preg_match('/^[0-9]{10}$/', $old['mobile'])) {
        $error = 'Mobile number must contain exactly 10 digits.';
    } elseif (strlen($old['message']) > 1000) {
        $error = 'Message is too long (max 1000 characters).';
    } elseif (save_csv('contacts.csv', ['Name', 'Email', 'Mobile', 'Message', 'Date'], [$old['name'], $old['email'], $old['mobile'], $old['message'], date('Y-m-d H:i:s')])) {
        $success = 'Thank you! Your message was submitted successfully.';
        $old = array_map(fn($v) => '', $old);
    } else {
        $error = 'Unable to save the data. Please try again.';
    }
}
include 'includes/header.php';
?>
<h1>Contact Us</h1>
<div class="two-col">
<section class="card">
    <?php if ($success): ?><p class="alert success"><?= e($success) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="alert error"><?= e($error) ?></p><?php endif; ?>
    <form method="POST" action="">
        <div class="form-group"><label for="name">Full Name</label><input type="text" id="name" name="name" value="<?= e($old['name']) ?>" required></div>
        <div class="form-group"><label for="email">Email Address</label><input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required></div>
        <div class="form-group"><label for="mobile">Mobile Number</label><input type="tel" id="mobile" name="mobile" pattern="[0-9]{10}" maxlength="10" value="<?= e($old['mobile']) ?>" required></div>
        <div class="form-group"><label for="message">Message</label><textarea id="message" name="message" rows="4" required><?= e($old['message']) ?></textarea></div>
        <button type="submit">Send Message</button>
    </form>
</section>
<aside class="card">
    <h2>CHARUSAT University</h2>
    <p>Changa, Gujarat, India</p>
    <p>Email: charusatuniversity@gmail.com</p>
    <p>Mobile: 9822456755</p>
    <p class="muted">Instagram: thecharusat &middot; Facebook: thecharusat &middot; YouTube: Charusat University</p>
</aside>
</div>
<?php include 'includes/footer.php'; ?>
