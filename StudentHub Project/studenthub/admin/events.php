<?php
// Practical 12: Event management CRUD with poster upload and file validation
$pageTitle = 'Manage Events';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
$cats = ['General', 'Workshop', 'Hackathon', 'Seminar', 'Cultural', 'Sports'];
const POSTER_DIR_REL = '/../uploads/posters/';
function save_poster($file, &$err) {      // returns null (no file), false (error) or the new file name
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) { $err = 'Upload failed (error code ' . $file['error'] . ').'; return false; }
    if ($file['size'] > 2 * 1024 * 1024) { $err = 'Poster must be 2 MB or smaller.'; return false; }
    $info = @getimagesize($file['tmp_name']);
    $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!$info || !isset($map[$info['mime']])) { $err = 'Only JPG, PNG or WEBP images are allowed.'; return false; }
    $name = 'poster_' . bin2hex(random_bytes(8)) . '.' . $map[$info['mime']];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . POSTER_DIR_REL . $name)) { $err = 'Could not save the poster file.'; return false; }
    return $name;
}
function drop_poster($name) { if ($name) @unlink(__DIR__ . POSTER_DIR_REL . basename($name)); }

$form = ['event_id' => '', 'event_name' => '', 'event_date' => '', 'venue' => '', 'category' => 'General', 'status' => 'open', 'description' => '', 'poster' => ''];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = filter_var($_POST['event_id'] ?? '', FILTER_VALIDATE_INT);
        if ($id) {
            $g = $conn->prepare('SELECT poster FROM events WHERE event_id = ?');
            $g->bind_param('i', $id); $g->execute(); $old = $g->get_result()->fetch_assoc(); $g->close();
            $s = $conn->prepare('DELETE FROM events WHERE event_id = ?');
            $s->bind_param('i', $id); $s->execute();
            if ($s->affected_rows === 1) { drop_poster($old['poster'] ?? ''); flash('success', 'Event deleted successfully.'); audit($conn, 'event_delete', 'Deleted event id ' . $id); }
            else { flash('error', 'Event not found.'); }
            $s->close();
        }
        redirect('admin/events.php');
    }
    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $_) { if ($k !== 'poster') $form[$k] = trim($_POST[$k] ?? ''); }
        $d = DateTime::createFromFormat('!Y-m-d', $form['event_date']);
        if (strlen($form['event_name']) < 3 || strlen($form['event_name']) > 100) $errors[] = 'Event name must be 3-100 characters.';
        if (!$d || $d->format('Y-m-d') !== $form['event_date']) $errors[] = 'Enter a valid event date.';
        if (strlen($form['venue']) < 2 || strlen($form['venue']) > 100) $errors[] = 'Venue must be 2-100 characters.';
        if (!in_array($form['category'], $cats, true)) $errors[] = 'Invalid category.';
        if (!in_array($form['status'], ['open', 'closed'], true)) $errors[] = 'Invalid status.';
        if (strlen($form['description']) > 500) $errors[] = 'Description is too long (max 500 characters).';
        $perr = ''; $newPoster = null;
        if (!$errors) {
            $newPoster = save_poster($_FILES['poster'] ?? [], $perr);
            if ($newPoster === false) $errors[] = $perr;
        }
        if (!$errors) {
            if ($action === 'add') {
                $s = $conn->prepare('INSERT INTO events (event_name, event_date, venue, category, status, poster, description) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $s->bind_param('sssssss', $form['event_name'], $form['event_date'], $form['venue'], $form['category'], $form['status'], $newPoster, $form['description']);
                $oldPoster = '';
            } else {
                $id = (int)$form['event_id'];
                $g = $conn->prepare('SELECT poster FROM events WHERE event_id = ?');
                $g->bind_param('i', $id); $g->execute(); $o = $g->get_result()->fetch_assoc(); $g->close();
                $oldPoster = $o['poster'] ?? '';
                $poster = $newPoster ?? $oldPoster;
                $s = $conn->prepare('UPDATE events SET event_name = ?, event_date = ?, venue = ?, category = ?, status = ?, poster = ?, description = ? WHERE event_id = ?');
                $s->bind_param('sssssssi', $form['event_name'], $form['event_date'], $form['venue'], $form['category'], $form['status'], $poster, $form['description'], $id);
            }
            if ($s->execute()) {
                if ($action === 'update' && $newPoster) drop_poster($oldPoster);
                flash('success', $action === 'add' ? 'Event added successfully!' : 'Event updated successfully!');
                audit($conn, 'event_' . $action, $form['event_name']);
                $s->close();
                redirect('admin/events.php');
            }
            $s->close();
            if ($newPoster) drop_poster($newPoster);
            $errors[] = 'Database error. Please try again.';
        }
    }
} elseif (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $s = $conn->prepare('SELECT event_id, event_name, event_date, venue, category, status, description, poster FROM events WHERE event_id = ?');
    $s->bind_param('i', $id); $s->execute();
    $row = $s->get_result()->fetch_assoc(); $s->close();
    if ($row) { $form = array_map(fn($v) => (string)$v, $row); }
}
$events = $conn->query('SELECT * FROM events ORDER BY event_date DESC');
include __DIR__ . '/../includes/header.php';
$editing = $form['event_id'] !== '';
?>
<h1>Event Management</h1>
<?php foreach ($errors as $er): ?><p class="alert error"><?= e($er) ?></p><?php endforeach; ?>
<section class="card">
    <h2><?= $editing ? 'Edit Event' : 'Add Event' ?></h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?= $editing ? 'update' : 'add' ?>">
        <input type="hidden" name="event_id" value="<?= e($form['event_id']) ?>">
        <div class="controls">
            <div class="control"><label for="event_name">Event Name</label><input id="event_name" name="event_name" value="<?= e($form['event_name']) ?>" required></div>
            <div class="control"><label for="event_date">Date</label><input type="date" id="event_date" name="event_date" value="<?= e($form['event_date']) ?>" required></div>
            <div class="control"><label for="venue">Venue</label><input id="venue" name="venue" value="<?= e($form['venue']) ?>" required></div>
            <div class="control"><label for="category">Category</label><select id="category" name="category"><?php foreach ($cats as $c): ?><option <?= $form['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
            <div class="control"><label for="status">Status</label><select id="status" name="status"><option value="open" <?= $form['status'] === 'open' ? 'selected' : '' ?>>Open</option><option value="closed" <?= $form['status'] === 'closed' ? 'selected' : '' ?>>Closed</option></select></div>
            <div class="control"><label for="poster">Poster (JPG/PNG/WEBP, max 2 MB)</label><input type="file" id="poster" name="poster" accept=".jpg,.jpeg,.png,.webp"></div>
        </div>
        <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" rows="3"><?= e($form['description']) ?></textarea></div>
        <button type="submit"><?= $editing ? 'Update Event' : 'Add Event' ?></button>
        <?php if ($editing): ?><a class="btn btn-outline" href="<?= e(url('admin/events.php')) ?>">Cancel</a><?php endif; ?>
    </form>
</section>
<h2 class="section-title">All Events</h2>
<div class="table-wrap"><table>
<tr><th>Poster</th><th>Name</th><th>Date</th><th>Venue</th><th>Category</th><th>Status</th><th>Actions</th></tr>
<?php if ($events->num_rows): while ($r = $events->fetch_assoc()): ?>
<tr><td><?php if ($r['poster']): ?><img class="thumb" src="<?= e(url('uploads/posters/' . $r['poster'])) ?>" alt="Poster of <?= e($r['event_name']) ?>"><?php else: ?>-<?php endif; ?></td>
<td><?= e($r['event_name']) ?></td><td><?= e($r['event_date']) ?></td><td><?= e($r['venue']) ?></td><td><?= e($r['category']) ?></td>
<td><span class="badge <?= $r['status'] === 'closed' ? 'closed' : '' ?>"><?= e($r['status']) ?></span></td>
<td class="actions"><a class="btn btn-sm" href="<?= e(url('admin/events.php?edit=' . (int)$r['event_id'])) ?>">Edit</a>
<form method="POST" action="" onsubmit="return confirm('Delete this event?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="event_id" value="<?= (int)$r['event_id'] ?>"><button type="submit" class="btn-sm btn-danger">Delete</button></form></td></tr>
<?php endwhile; else: ?><tr><td colspan="7">No events yet.</td></tr><?php endif; ?>
</table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
