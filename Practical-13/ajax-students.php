<?php
$pageTitle = 'Students (AJAX)'; $extraJs = ['students-ajax.js'];
require_once __DIR__ . '/includes/auth.php';
require_role('admin');
include __DIR__ . '/includes/header.php';
?>
<h1>Student CRUD without page reload</h1>
<p class="muted">This page uses the Fetch API with the JSON endpoint <code>api/students.php</code>.</p>
<div id="ajaxApp" data-api="<?= e(url('api/students.php')) ?>">
    <p id="msg" class="alert info" role="status" aria-live="polite" hidden></p>
    <section class="card">
        <h2 id="formTitle">Add Student</h2>
        <form id="sForm" novalidate>
            <input type="hidden" id="student_id">
            <div class="controls">
                <div class="control"><label for="name">Name</label><input id="name" required></div>
                <div class="control"><label for="email">Email</label><input type="email" id="email" required></div>
                <div class="control"><label for="course">Course</label><select id="course"><option>B.Tech</option><option>BCA</option><option>B.Sc IT</option><option>MCA</option></select></div>
                <div class="control"><label for="year">Year</label><input type="number" id="year" min="1" max="4" value="1" required></div>
                <div class="control"><label for="status">Status</label><select id="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <button type="submit" id="saveBtn">Add Student</button> <button type="button" id="cancelBtn" class="btn-outline" hidden>Cancel edit</button>
        </form>
    </section>
    <h2 class="section-title">Students</h2>
    <div class="control" style="max-width:340px;margin-bottom:12px"><label for="q">Live search</label><input type="search" id="q" placeholder="Name, email or course"></div>
    <div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Status</th><th>Actions</th></tr></thead><tbody id="tbody"></tbody></table></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
