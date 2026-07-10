<?php
session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Maintenance Officer");
?>

<!DOCTYPE html>
<html>

<head>

<title>Assigned Jobs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Assigned Jobs</h2>

<hr>

<table class="table table-bordered">

<thead>

<tr>

<th>ID</th>

<th>Category</th>

<th>Location</th>

<th>Status</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<tr>

<td colspan="5" class="text-center">

No Assigned Jobs Yet

</td>

</tr>

</tbody>

</table>

<a href="dashboard.php"

class="btn btn-primary">

Back

</a>

</div>

</body>

</html>