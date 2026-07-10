<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();

if (!isset($_GET['id']) || empty($_GET['id'])) {

    $_SESSION['error'] = "Invalid request selected.";

    header("Location: my_requests.php");
    exit();
}

$requestModel = new ServiceRequest();

$request = $requestModel->getRequestById($_GET['id']);

if (!$request) {

    $_SESSION['error'] = "Maintenance request not found.";

    header("Location: my_requests.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Prevent users from viewing other users' requests
|--------------------------------------------------------------------------
*/

if ($request['user_id'] != $_SESSION['user_id']) {

    $_SESSION['error'] = "Access denied.";

    header("Location: my_requests.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Request Details | UMMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body class="bg-light">

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

<p>

<strong>Ticket Number</strong><br>

<?= htmlspecialchars($request['ticket_number']); ?>

</p>

<p>

<strong>Category</strong><br>

<?= htmlspecialchars($request['category_name']); ?>

</p>

<p>

<strong>Title</strong><br>

<?= htmlspecialchars($request['title']); ?>

</p>

<p>

<strong>Priority</strong><br>

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

</p>

<p>

<strong>Status</strong><br>

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

</p>

</div>

<div class="col-md-6">

<p>

<strong>Location</strong><br>

<?= htmlspecialchars($request['location']); ?>

</p>

<p>

<strong>Building</strong><br>

<?= htmlspecialchars($request['building'] ?: 'N/A'); ?>

</p>

<p>

<strong>Room Number</strong><br>

<?= htmlspecialchars($request['room_number'] ?: 'N/A'); ?>

</p>

<p>

<strong>Date Submitted</strong><br>

<?= date("d M Y H:i", strtotime($request['created_at'])); ?>

</p>

<?php if(!empty($request['completed_at'])): ?>

<p>

<strong>Date Completed</strong><br>

<?= date("d M Y H:i", strtotime($request['completed_at'])); ?>

</p>

<?php endif; ?>

</div>

</div>

<hr>

<h5>Description</h5>

<div class="alert alert-light border">

<?= nl2br(htmlspecialchars($request['description'])); ?>

</div>

<hr>

<h5>Uploaded Image</h5>

<?php if(!empty($request['image'])): ?>

<img
src="../../assets/uploads/maintenance/<?= htmlspecialchars($request['image']); ?>"
class="img-fluid rounded shadow"
style="max-height:400px;">

<?php else: ?>

<div class="alert alert-secondary">

No image uploaded.

</div>

<?php endif; ?>

<hr>

<a href="my_requests.php"

class="btn btn-secondary">

← Back to My Requests

</a>

</div>

</div>

</div>

</body>

</html>