<?php
// Public view of the events managed in events.php
$pageTitle = 'Events';
require_once __DIR__ . '/includes/auth.php';
$events = $conn->query('SELECT event_name, event_date, venue, category, status, poster, description FROM events ORDER BY event_date DESC');
include __DIR__ . '/includes/header.php';
?>
<h1>Events</h1>
<div class="grid">
<?php if ($events->num_rows): while ($ev = $events->fetch_assoc()): ?>
    <article class="card event-card">
        <?php if ($ev['poster']): ?><img src="<?= e(url('uploads/posters/' . $ev['poster'])) ?>" alt="Poster for <?= e($ev['event_name']) ?>"><?php endif; ?>
        <h3><?= e($ev['event_name']) ?></h3>
        <p class="muted"><?= e($ev['event_date']) ?> &middot; <?= e($ev['venue']) ?> &middot; <?= e($ev['category']) ?></p>
        <p><?= e($ev['description']) ?></p>
        <span class="badge <?= $ev['status'] === 'closed' ? 'closed' : '' ?>"><?= e(ucfirst($ev['status'])) ?></span>
    </article>
<?php endwhile; else: ?><p class="muted">No events yet.</p><?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
