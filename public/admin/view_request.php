<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();

RoleMiddleware::authorize("Administrator");

if (!isset($_GET['id'])) {

    header("Location: manage_requests.php");
    exit();
}

$requestModel = new ServiceRequest();

$request = $requestModel->getRequestDetails($_GET['id']);

if (!$request) {

    die("Maintenance request not found.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>View Maintenance Request</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="../../assets/css/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary">

<div class="container">

<span class="navbar-brand">

University Maintenance Management System

</span>

<div>

Welcome,

<strong>

<?= htmlspecialchars($_SESSION['firstname']); ?>

</strong>

<a href="../../app/controllers/AuthController.php?action=logout"
class="btn btn-danger btn-sm ms-3">

Logout

</a>

</div>

</div>

</nav>

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

Maintenance Request Details

</h3>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6">

<h5 class="text-primary">

Request Information

</h5>

<table class="table table-bordered">

<tr>

<th>Ticket Number</th>

<td><?= htmlspecialchars($request['ticket_number']); ?></td>

</tr>

<tr>

<th>Category</th>

<td><?= htmlspecialchars($request['category_name']); ?></td>

</tr>

<tr>

<th>Priority</th>

<td>

<span class="badge bg-danger">

<?= htmlspecialchars($request['priority']); ?>

</span>

</td>

</tr>

<tr>

<th>Status</th>

<td>

<span class="badge bg-warning text-dark">

<?= htmlspecialchars($request['status']); ?>

</span>

</td>

</tr>

<tr>

<th>Date Submitted</th>

<td><?= $request['created_at']; ?></td>

</tr>

</table>

</div>

<div class="col-md-6">

<h5 class="text-success">

Student Information

</h5>

<table class="table table-bordered">

<tr>

<th>Name</th>

<td><?= htmlspecialchars($request['fullname']); ?></td>

</tr>

<tr>

<th>Email</th>

<td><?= htmlspecialchars($request['email']); ?></td>

</tr>

<tr>

<th>Phone</th>

<td><?= htmlspecialchars($request['phone']); ?></td>

</tr>

<tr>

<th>Department</th>

<td><?= htmlspecialchars($request['department']); ?></td>

</tr>

</table>

</div>

</div>

<hr>

<h5 class="text-primary">

Location

</h5>

<table class="table table-bordered">

<tr>

<th>Location</th>

<td><?= htmlspecialchars($request['location']); ?></td>

</tr>

<tr>

<th>Building</th>

<td><?= htmlspecialchars($request['building']); ?></td>

</tr>

<tr>

<th>Room Number</th>

<td><?= htmlspecialchars($request['room_number']); ?></td>

</tr>

</table>

<hr>

<h5 class="text-primary">

Maintenance Issue

</h5>

<table class="table table-bordered">

<tr>

<th width="200">Title</th>

<td><?= htmlspecialchars($request['title']); ?></td>

</tr>

<tr>

<th>Description</th>

<td><?= nl2br(htmlspecialchars($request['description'])); ?></td>

</tr>

</table>

<hr>

<h5 class="text-primary">

Attached Image

</h5>

<?php if(!empty($request['image'])): ?>

<img
src="../../assets/uploads/maintenance/<?= htmlspecialchars($request['image']); ?>"
class="img-fluid rounded border"
style="max-height:350px;">

<?php else: ?>

<div class="alert alert-secondary">

No image uploaded.

</div>

<?php endif; ?>

<hr>

<a href="assign_request.php?id=<?= $request['request_id']; ?>"
class="btn btn-success">

Assign Maintenance Officer

</a>

<a href="manage_requests.php"
class="btn btn-secondary">

Back

</a>

</div>

</div>

</div>

</body>

</html>