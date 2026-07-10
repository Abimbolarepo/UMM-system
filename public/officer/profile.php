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

<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Officer Profile</h2>

<hr>

<table class="table table-bordered">

<tr>

<th>First Name</th>

<td><?= $_SESSION['firstname']; ?></td>

</tr>

<tr>

<th>Last Name</th>

<td><?= $_SESSION['lastname']; ?></td>

</tr>

<tr>

<th>Email</th>

<td><?= $_SESSION['email']; ?></td>

</tr>

<tr>

<th>Role</th>

<td><?= $_SESSION['role_name']; ?></td>

</tr>

</table>

<a href="dashboard.php"

class="btn btn-primary">

Back

</a>

</div>

</body>

</html>