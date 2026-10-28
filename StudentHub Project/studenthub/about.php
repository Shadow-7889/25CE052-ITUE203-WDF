<?php $pageTitle = 'About'; include 'includes/header.php'; ?>
<h1>About StudentHub</h1>
<div class="two-col">
    <section class="card">
        <h2>Our Mission</h2>
        <p>StudentHub is an online academic portal where students and administrators manage academic activities from one place: registration, events, notices, attendance, assignments and results.</p>
        <h2>User Roles</h2>
        <ul><li><strong>Student</strong> - registers, logs in, views the dashboard and registers for events.</li>
            <li><strong>Admin</strong> - manages students and events and monitors the dashboard.</li></ul>
        <h2>Key Modules</h2>
        <p>Authentication, Student Management, Event Management with poster upload, JSON API with AJAX, and an Admin Dashboard.</p>
    </section>
    <aside class="card">
        <h2>Quick Facts</h2>
        <ul><li>Built with HTML5, CSS3, JavaScript, PHP and MySQL</li><li>Responsive and accessible layout</li><li>Dark / light theme</li><li>Secure password hashing and sessions</li></ul>
        <p><a class="btn" href="<?= e(url('contact.php')) ?>">Contact us</a></p>
    </aside>
</div>
<?php include 'includes/footer.php'; ?>
