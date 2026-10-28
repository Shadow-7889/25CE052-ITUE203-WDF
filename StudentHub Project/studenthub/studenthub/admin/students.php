<?php
// Practical 11: Student management CRUD with search, filter and pagination (mysqli prepared statements)
$pageTitle = 'Manage Students';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
$courses = ['B.Tech', 'BCA', 'B.Sc IT', 'MCA'];
function valid_student($d, $courses) {
    $e = [];
    if (!preg_match("/^[A-Za-z][A-Za-z .'-]{1,59}$/", $d['name'])) $e[] = 'Enter a valid name (letters only, 2-60 characters).';
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $e[] = 'Enter a valid email address.';
    if (!in_array($d['course'], $courses, true)) $e[] = 'Select a valid course.';
    if (!preg_match('/^[1-4]$/', $d['year'])) $e[] = 'Year must be between 1 and 4.';
    if (!in_array($d['status'], ['active', 'inactive'], true)) $e[] = 'Invalid status.';
    return $e;
}
$form = ['student_id' => '', 'name' => '', 'email' => '', 'course' => 'B.Tech', 'year' => '1', 'status' => 'active'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = filter_var($_POST['student_id'] ?? '', FILTER_VALIDATE_INT);
        if ($id) {
            $s = $conn->prepare('DELETE FROM students WHERE student_id = ?');
            $s->bind_param('i', $id); $s->execute();
            if ($s->affected_rows === 1) { flash('success', 'Student deleted successfully.'); audit($conn, 'student_delete', 'Deleted student id ' . $id); }
            else { flash('error', 'Student not found or could not be deleted.'); }
            $s->close();
        }
        redirect('admin/students.php');
    }
    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $_) { $form[$k] = trim($_POST[$k] ?? ''); }
        $errors = valid_student($form, $courses);
        if (!$errors) {
            $year = (int)$form['year'];
            if ($action === 'add') {
                $s = $conn->prepare('INSERT INTO students (name, email, course, year, status) VALUES (?, ?, ?, ?, ?)');
                $s->bind_param('sssis', $form['name'], $form['email'], $form['course'], $year, $form['status']);
            } else {
                $id = (int)$form['student_id'];
                $s = $conn->prepare('UPDATE students SET name = ?, email = ?, course = ?, year = ?, status = ? WHERE student_id = ?');
                $s->bind_param('sssisi', $form['name'], $form['email'], $form['course'], $year, $form['status'], $id);
            }
            if ($s->execute()) {
                flash('success', $action === 'add' ? 'Student added successfully!' : 'Student updated successfully!');
                audit($conn, 'student_' . $action, $form['name'] . ' (' . $form['email'] . ')');
                $s->close();
                redirect('admin/students.php');
            }
            $errors[] = $s->errno === 1062 ? 'A student with this email already exists.' : 'Database error. Please try again.';
            $s->close();
        }
    }
} elseif (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $s = $conn->prepare('SELECT student_id, name, email, course, year, status FROM students WHERE student_id = ?');
    $s->bind_param('i', $id); $s->execute();
    $row = $s->get_result()->fetch_assoc(); $s->close();
    if ($row) { $form = array_map('strval', $row); }
}
// search + filter + pagination
$q = trim($_GET['q'] ?? ''); $cf = trim($_GET['course'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1)); $per = 8;
$where = ' WHERE 1=1'; $types = ''; $args = [];
if ($q !== '') { $where .= ' AND (name LIKE ? OR email LIKE ? OR course LIKE ?)'; $types .= 'sss'; $l = '%' . $q . '%'; array_push($args, $l, $l, $l); }
if ($cf !== '') { $where .= ' AND course = ?'; $types .= 's'; $args[] = $cf; }
$cnt = $conn->prepare('SELECT COUNT(*) FROM students' . $where);
if ($types) $cnt->bind_param($types, ...$args);
$cnt->execute(); $cnt->bind_result($total); $cnt->fetch(); $cnt->close();
$pages = max(1, (int)ceil($total / $per)); $page = min($page, $pages); $off = ($page - 1) * $per;
$st = $conn->prepare('SELECT student_id, name, email, course, year, status FROM students' . $where . ' ORDER BY student_id DESC LIMIT ? OFFSET ?');
$a2 = array_merge($args, [$per, $off]);
$st->bind_param($types . 'ii', ...$a2);
$st->execute(); $rows = $st->get_result();
include __DIR__ . '/../includes/header.php';
$editing = $form['student_id'] !== '';
?>
<h1>Student Management</h1>
<?php foreach ($errors as $er): ?><p class="alert error"><?= e($er) ?></p><?php endforeach; ?>
<section class="card">
    <h2><?= $editing ? 'Edit Student' : 'Add Student' ?></h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?= $editing ? 'update' : 'add' ?>">
        <input type="hidden" name="student_id" value="<?= e($form['student_id']) ?>">
        <div class="controls">
            <div class="control"><label for="name">Name</label><input id="name" name="name" value="<?= e($form['name']) ?>" required></div>
            <div class="control"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= e($form['email']) ?>" required></div>
            <div class="control"><label for="fcourse">Course</label><select id="fcourse" name="course"><?php foreach ($courses as $c): ?><option <?= $form['course'] === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
            <div class="control"><label for="year">Year</label><input type="number" id="year" name="year" min="1" max="4" value="<?= e($form['year']) ?>" required></div>
            <div class="control"><label for="status">Status</label><select id="status" name="status"><option value="active" <?= $form['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $form['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
        </div>
        <button type="submit"><?= $editing ? 'Update Student' : 'Add Student' ?></button>
        <?php if ($editing): ?><a class="btn btn-outline" href="<?= e(url('admin/students.php')) ?>">Cancel</a><?php endif; ?>
    </form>
</section>
<h2 class="section-title">Student Records (<?= (int)$total ?>)</h2>
<form method="GET" class="toolbar" aria-label="Search students">
    <div class="control"><label for="q">Search name / email / course</label><input type="search" id="q" name="q" value="<?= e($q) ?>"></div>
    <div class="control"><label for="course">Course</label><select id="course" name="course"><option value="">All</option><?php foreach ($courses as $c): ?><option <?= $cf === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?></select></div>
    <button type="submit">Search</button> <a class="btn btn-outline" href="<?= e(url('admin/students.php')) ?>">Reset</a>
</form>
<div class="table-wrap"><table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Status</th><th>Actions</th></tr>
<?php if ($rows->num_rows): while ($r = $rows->fetch_assoc()): ?>
<tr><td><?= (int)$r['student_id'] ?></td><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['course']) ?></td><td><?= (int)$r['year'] ?></td>
<td><span class="badge <?= $r['status'] === 'inactive' ? 'closed' : '' ?>"><?= e($r['status']) ?></span></td>
<td class="actions"><a class="btn btn-sm" href="<?= e(url('admin/students.php?edit=' . (int)$r['student_id'])) ?>">Edit</a>
<form method="POST" action="" onsubmit="return confirm('Delete this student permanently?');"><input type="hidden" name="action" value="delete"><input type="hidden" name="student_id" value="<?= (int)$r['student_id'] ?>"><button type="submit" class="btn-sm btn-danger">Delete</button></form></td></tr>
<?php endwhile; else: ?><tr><td colspan="7">No students found.</td></tr><?php endif; ?>
</table></div>
<nav class="pagination" aria-label="Pages">
    <?php if ($page > 1): ?><a class="btn btn-sm" href="?<?= e(http_build_query(['q' => $q, 'course' => $cf, 'page' => $page - 1])) ?>">&larr; Previous</a><?php endif; ?>
    <span>Page <?= $page ?> of <?= $pages ?></span>
    <?php if ($page < $pages): ?><a class="btn btn-sm" href="?<?= e(http_build_query(['q' => $q, 'course' => $cf, 'page' => $page + 1])) ?>">Next &rarr;</a><?php endif; ?>
</nav>
<?php include __DIR__ . '/../includes/footer.php'; ?>
