<?php
$pageTitle = 'Assignments';
require_once 'includes/auth.php';
require_login();
include 'includes/header.php';
?>
<h1>Assignments</h1>
<div class="table-wrap">
<table>

<tr>
<th>Subject</th>
<th>Assignment</th>
<th>Due Date</th>
<th>Status</th>
</tr>


<tr>
<td>Web Development</td>
<td>HTML Forms</td>
<td>10-Aug-2026</td>
<td>Pending</td>
</tr>

<tr>
<td>Java</td>
<td>OOP Program</td>
<td>15-Aug-2026</td>
<td>Submitted</td>
</tr>


<tr>
<td>Python</td>
<td>File Handling</td>
<td>20-Aug-2026</td>
<td>Pending</td>
</tr>

</table>
</div>
<?php include 'includes/footer.php'; ?>
