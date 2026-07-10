<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();

$requestModel = new ServiceRequest();

$requests = $requestModel->getStudentRequests($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>My Maintenance Requests</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<div class="d-flex justify-content-between align-items-center">

<h3>📋 My Maintenance Requests</h3>

<a href="dashboard.php" class="btn btn-light btn-sm">

← Dashboard

</a>

</div>

</div>

<div class="card-body">

<?php if(isset($_SESSION['success'])): ?>

<div class="alert alert-success">

<?= $_SESSION['success']; ?>

</div>

<?php unset($_SESSION['success']); ?>

<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>

<div class="alert alert-danger">

<?= $_SESSION['error']; ?>

</div>

<?php unset($_SESSION['error']); ?>

<?php endif; ?>

<?php if(count($requests)==0): ?>

<div class="alert alert-info">

You have not submitted any maintenance requests.

</div>

<?php else: ?>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Ticket</th>

<th>Category</th>

<th>Title</th>

<th>Priority</th>

<th>Status</th>

<th>Date Submitted</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php foreach($requests as $request): ?>

<tr>

<td>

<?= htmlspecialchars($request['ticket_number']); ?>

</td>

<td>

<?= htmlspecialchars($request['category_name']); ?>

</td>

<td>

<?= htmlspecialchars($request['title']); ?>

</td>

<td>

<?php

switch($request['priority']){

case "Low":

echo '<span class="badge bg-success">Low</span>';

break;

case "Medium":

echo '<span class="badge bg-warning text-dark">Medium</span>';

break;

case "High":

echo '<span class="badge bg-danger">High</span>';

break;

default:

echo '<span class="badge bg-dark">Critical</span>';

}

?>

</td>

<td>

<?php

switch($request['status']){

case "Pending":

echo '<span class="badge bg-warning text-dark">Pending</span>';

break;

case "Assigned":

echo '<span class="badge bg-info">Assigned</span>';

break;

case "In Progress":

echo '<span class="badge bg-primary">In Progress</span>';

break;

case "Completed":

echo '<span class="badge bg-success">Completed</span>';

break;

default:

echo '<span class="badge bg-secondary">Cancelled</span>';

}

?>

</td>

<td>

<?= date("d M Y",strtotime($request['created_at'])); ?>

</td>

<td>

<a href="request_details.php?id=<?= $request['request_id']; ?>"

class="btn btn-sm btn-primary">

View

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

</div>

</div>

</div>

</body>

</html>