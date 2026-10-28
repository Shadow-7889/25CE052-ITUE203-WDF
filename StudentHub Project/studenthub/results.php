<?php
$pageTitle = 'Results';
require_once 'includes/auth.php';
require_login();
include 'includes/header.php';
?>
<h1>Semester Result</h1>
<div class="table-wrap">
<table>

<tr>
<th>Subject</th>
<th>Marks</th>
<th>Grade</th>
</tr>

<tr>
<td>Web Development</td>
<td>88</td>
<td>A</td>
</tr>

<tr>
<td>Java</td>
<td>82</td>
<td>A</td>
</tr>

<tr>
<td>Python</td>
<td>90</td>
<td>A+</td>
</tr>

</table>
</div>
<?php include 'includes/footer.php'; ?>
