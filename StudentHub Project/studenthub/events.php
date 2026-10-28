<?php
// Events list (Practicals 8 + 12 view): students can register for open events
$pageTitle = 'Events';
require_once 'includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register_event') {
    require_login();
    $eventId = filter_var($_POST['event_id'] ?? '', FILTER_VALIDATE_INT);
    $q = $conn->prepare('SELECT student_id FROM students WHERE user_id = ?');
    $q->bind_param('i', $_SESSION['user_id']); $q->execute();
    $stu = $q->get_result()->fetch_assoc(); $q->close();
    if (!$stu) { flash('error', 'Only student accounts can register for events.'); }
    elseif (!$eventId) { flash('error', 'Invalid event selected.'); }
    else {
        $s = $conn->prepare("INSERT INTO registrations (student_id, event_id) SELECT ?, event_id FROM events WHERE event_id = ? AND status = 'open'");
        $s->bind_param('ii', $stu['student_id'], $eventId);
        if ($s->execute() && $s->affected_rows === 1) { flash('success', 'You are registered for the event!'); }
        elseif ($s->errno === 1062) { flash('error', 'You are already registered for this event.'); }
        else { flash('error', 'Registration failed. The event may be closed.'); }
        $s->close();
    }
    redirect('events.php');
}
$cat = trim($_GET['category'] ?? ''); $stat = trim($_GET['status'] ?? '');
$sql = 'SELECT event_id, event_name, event_date, venue, category, status, poster, description FROM events WHERE 1=1';
$types = ''; $args = [];
if ($cat !== '') { $sql .= ' AND category = ?'; $types .= 's'; $args[] = $cat; }
if (in_array($stat, ['open', 'closed'], true)) { $sql .= ' AND status = ?'; $types .= 's'; $args[] = $stat; }
$sql .= ' ORDER BY event_date DESC';
$st = $conn->prepare($sql);
if ($types) $st->bind_param($types, ...$args);
$st->execute(); $events = $st->get_result();
$cats = $conn->query('SELECT DISTINCT category FROM events ORDER BY category');
include 'includes/header.php';
?>
<h1>Upcoming Events</h1>
<form method="GET" class="controls" aria-label="Filter events">
    <div class="control"><label for="category">Category</label><select id="category" name="category"><option value="">All</option>
        <?php while ($c = $cats->fetch_assoc()): ?><option <?= $cat === $c['category'] ? 'selected' : '' ?>><?= e($c['category']) ?></option><?php endwhile; ?></select></div>
    <div class="control"><label for="status">Status</label><select id="status" name="status"><option value="">All</option>
        <option value="open" <?= $stat === 'open' ? 'selected' : '' ?>>Open</option><option value="closed" <?= $stat === 'closed' ? 'selected' : '' ?>>Closed</option></select></div>
    <div class="control"><label>&nbsp;</label><button type="submit">Apply Filter</button></div>
</form>
<div class="grid">
<?php if ($events->num_rows): while ($ev = $events->fetch_assoc()): ?>
    <article class="card event-card">
        <?php if ($ev['poster']): ?><img src="<?= e(url('uploads/posters/' . $ev['poster'])) ?>" alt="Poster for <?= e($ev['event_name']) ?>"><?php endif; ?>
        <h3><?= e($ev['event_name']) ?></h3>
        <p class="muted"><?= e($ev['event_date']) ?> &middot; <?= e($ev['venue']) ?> &middot; <?= e($ev['category']) ?></p>
        <p><?= e($ev['description']) ?></p>
        <span class="badge <?= $ev['status'] === 'closed' ? 'closed' : '' ?>"><?= e(ucfirst($ev['status'])) ?></span>
        <?php if ($ev['status'] === 'open'): ?>
            <?php if (is_logged_in() && !is_admin()): ?>
                <form method="POST" action="" style="margin-top:10px"><input type="hidden" name="action" value="register_event"><input type="hidden" name="event_id" value="<?= (int)$ev['event_id'] ?>"><button type="submit" class="btn-sm">Register</button></form>
            <?php elseif (!is_logged_in()): ?><p><a href="<?= e(url('login.php')) ?>">Login to register</a></p><?php endif; ?>
        <?php endif; ?>
    </article>
<?php endwhile; else: ?><p class="muted">No events match your filter.</p><?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
