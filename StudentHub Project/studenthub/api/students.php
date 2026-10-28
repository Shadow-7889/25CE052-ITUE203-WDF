<?php
// Practical 13: JSON API for students (GET list/search, POST add/update/delete) - admin only
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json; charset=utf-8');
function out($code, $ok, $msg, $data = null) {
    http_response_code($code);
    echo json_encode(['success' => $ok, 'message' => $msg, 'data' => $data]);
    exit;
}
if (!is_logged_in()) out(401, false, 'Please log in first.');
if (!is_admin()) out(403, false, 'Admin access required.');
$courses = ['B.Tech', 'BCA', 'B.Sc IT', 'MCA'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $q = trim($_GET['q'] ?? ''); $like = '%' . $q . '%';
    $s = $conn->prepare('SELECT student_id, name, email, course, year, status FROM students WHERE name LIKE ? OR email LIKE ? OR course LIKE ? ORDER BY student_id DESC LIMIT 100');
    $s->bind_param('sss', $like, $like, $like);
    $s->execute();
    out(200, true, 'Students loaded.', $s->get_result()->fetch_all(MYSQLI_ASSOC));
}
if ($method !== 'POST') out(405, false, 'Method not allowed.');
if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== 0) out(415, false, 'Content-Type must be application/json.');
$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) out(400, false, 'Invalid JSON body.');
$action = $in['action'] ?? '';

if ($action === 'delete') {
    $id = filter_var($in['student_id'] ?? '', FILTER_VALIDATE_INT);
    if (!$id) out(400, false, 'Invalid student id.');
    $s = $conn->prepare('DELETE FROM students WHERE student_id = ?');
    $s->bind_param('i', $id); $s->execute();
    if ($s->affected_rows !== 1) out(404, false, 'Student not found.');
    audit($conn, 'api_student_delete', 'Deleted student id ' . $id);
    out(200, true, 'Student deleted.');
}
if ($action === 'add' || $action === 'update') {
    $d = [];
    foreach (['name', 'email', 'course', 'year', 'status'] as $k) { $d[$k] = trim((string)($in[$k] ?? '')); }
    if (!preg_match("/^[A-Za-z][A-Za-z .'-]{1,59}$/", $d['name'])) out(400, false, 'Enter a valid name (letters only, 2-60 characters).');
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) out(400, false, 'Enter a valid email address.');
    if (!in_array($d['course'], $courses, true)) out(400, false, 'Select a valid course.');
    if (!preg_match('/^[1-4]$/', $d['year'])) out(400, false, 'Year must be between 1 and 4.');
    if (!in_array($d['status'], ['active', 'inactive'], true)) out(400, false, 'Invalid status.');
    $year = (int)$d['year'];
    if ($action === 'add') {
        $s = $conn->prepare('INSERT INTO students (name, email, course, year, status) VALUES (?, ?, ?, ?, ?)');
        $s->bind_param('sssis', $d['name'], $d['email'], $d['course'], $year, $d['status']);
    } else {
        $id = filter_var($in['student_id'] ?? '', FILTER_VALIDATE_INT);
        if (!$id) out(400, false, 'Invalid student id.');
        $s = $conn->prepare('UPDATE students SET name = ?, email = ?, course = ?, year = ?, status = ? WHERE student_id = ?');
        $s->bind_param('sssisi', $d['name'], $d['email'], $d['course'], $year, $d['status'], $id);
    }
    if (!$s->execute()) out($s->errno === 1062 ? 409 : 500, false, $s->errno === 1062 ? 'A student with this email already exists.' : 'Database error.');
    audit($conn, 'api_student_' . $action, $d['name']);
    out($action === 'add' ? 201 : 200, true, $action === 'add' ? 'Student added.' : 'Student updated.');
}
out(400, false, 'Unknown action.');
