<?php
$pageTitle = 'FAQ';
$faqs = [
 ['How do I register?', 'Open the Register page, fill in the form and submit. Then log in with your email and password.'],
 ['How do I find my profile?', 'After logging in, use the Profile link in the menu.'],
 ['How do I join events?', 'Visit the Events page. Logged-in students can register for any open event.'],
 ['Is the website mobile-friendly?', 'Yes. CSS Grid, Flexbox and media queries adapt the layout to mobile, tablet and desktop.'],
 ['How does dark mode work?', 'JavaScript toggles a CSS class and saves your choice in localStorage so it is remembered.'],
 ['I forgot my session - why was I logged out?', 'For security, sessions expire after 30 minutes of inactivity.'],
];
include 'includes/header.php';
?>
<h1>Frequently Asked Questions</h1>
<?php foreach ($faqs as $f): ?>
<div class="faq-item">
    <button type="button" class="faq-question" aria-expanded="false"><?= e($f[0]) ?><span>+</span></button>
    <div class="faq-answer"><p><?= e($f[1]) ?></p></div>
</div>
<?php endforeach; ?>
<?php include 'includes/footer.php'; ?>
