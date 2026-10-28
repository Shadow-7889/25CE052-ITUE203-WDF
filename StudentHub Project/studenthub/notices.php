<?php
$pageTitle = 'Notice Board';
require_once 'includes/auth.php';
include 'includes/header.php';
?>
<h1>Notice Board</h1>
<div class="table-wrap">
<table>
    <tr>
        <th>Date</th>
        <th>Notice</th>
    </tr>

    <tr>
        <td>01-08-2026</td>
        <td>Semester examinations will begin from 20-08-2026.</td>
    </tr>

    <tr>
        <td>02-08-2026</td>
        <td>Assignment submission deadline is 10-08-2026.</td>
    </tr>

    <tr>
        <td>03-08-2026</td>
        <td>College will remain closed on 15-08-2026 for Independence Day.</td>
    </tr>

    <tr>
        <td>04-08-2026</td>
        <td>Annual sports registration is now open.</td>
    </tr>
</table>
</div>
<?php include 'includes/footer.php'; ?>
