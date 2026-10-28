<?php
$pageTitle = 'Home'; $showBanner = true;
include 'includes/header.php';
$latest = $conn->query("SELECT event_id, event_name, event_date, venue, status FROM events ORDER BY event_date DESC LIMIT 3");
?>
<section class="slider" aria-label="Highlights">
    <div class="slide active"><h1>Welcome to StudentHub</h1><p>Connect, learn and grow with your campus community.</p><button class="btn openModal" type="button">Get Started</button></div>
    <div class="slide"><h1>Discover New Events</h1><p>Workshops, hackathons and career fairs in one place.</p><a class="btn" href="<?= e(url('events.php')) ?>">View Events</a></div>
    <div class="slide"><h1>Explore Campus Data</h1><p>Search notices, student profiles and FAQs instantly.</p><a class="btn" href="<?= e(url('explorer.php')) ?>">Open Explorer</a></div>
    <button class="slider-btn prev" id="prevSlide" type="button" aria-label="Previous slide">&#10094;</button>
    <button class="slider-btn next" id="nextSlide" type="button" aria-label="Next slide">&#10095;</button>
    <div class="dots"><button class="dot active" type="button" aria-label="Slide 1"></button><button class="dot" type="button" aria-label="Slide 2"></button><button class="dot" type="button" aria-label="Slide 3"></button></div>
</section>

<h2 class="section-title">Features</h2>
<div class="grid">
    <article class="card"><h3>Student Registration</h3><p>Create a secure account with validated details.</p><a href="<?= e(url('register.php')) ?>">Register</a></article>
    <article class="card"><h3>Events</h3><p>Browse upcoming events and register in one click.</p><a href="<?= e(url('events.php')) ?>">See events</a></article>
    <article class="card"><h3>Dashboard</h3><p>Attendance, assignments, results and timetable.</p><a href="<?= e(url('dashboard.php')) ?>">Open dashboard</a></article>
    <article class="card"><h3>Notice Board</h3><p>Stay updated with the latest college notices.</p><a href="<?= e(url('notices.php')) ?>">Read notices</a></article>
</div>

<h2 class="section-title">Latest Events</h2>
<div class="grid">
<?php if ($latest && $latest->num_rows): while ($ev = $latest->fetch_assoc()): ?>
    <article class="card"><h3><?= e($ev['event_name']) ?></h3><p class="muted"><?= e($ev['event_date']) ?> &middot; <?= e($ev['venue']) ?></p><span class="badge <?= $ev['status'] === 'closed' ? 'closed' : '' ?>"><?= e(ucfirst($ev['status'])) ?></span></article>
<?php endwhile; else: ?>
    <p class="muted">No events yet. Import sql/studenthub.sql to load sample data.</p>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
