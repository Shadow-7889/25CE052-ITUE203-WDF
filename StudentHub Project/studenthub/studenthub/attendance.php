<?php
$pageTitle = 'Attendance';
require_once 'includes/auth.php';
require_login();
include 'includes/header.php';
?>
<h1>Attendance</h1>
<div class="table-wrap">
<table>
<tr>
<th>Subject</th>
<th>Total Classes</th>
<th>Attended</th>
<th>Percentage</th>
</tr>

<tr>
<td>Web Development</td>
<td>30</td>
<td>28</td>
<td>93%</td>
</tr>

<tr>
<td>Java</td>
<td>28</td>
<td>26</td>
<td>92%</td>
</tr>

<tr>
<td>Python</td>
<td>32</td>
<td>30</td>
<td>94%</td>
</tr>

</table>
</div>
<?php include 'includes/footer.php'; ?>
