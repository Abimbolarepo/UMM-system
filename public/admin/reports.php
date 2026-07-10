<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$requestModel = new ServiceRequest();

$stats = $requestModel->getReportStatistics();

$categories = $requestModel->getRequestsByCategory();

$recentRequests = $requestModel->getRecentActivities(10);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Reports | UMMS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../../assets/css/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow">

    <div class="container">

        <span class="navbar-brand fw-bold">

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

<div class="container mt-4">

<h2 class="fw-bold">

Reports Dashboard

</h2>

<p class="text-muted">

System-wide maintenance request statistics

</p>

<p class="text-muted mb-0">

Generated on <?= date("d M Y \a\\t h:i A"); ?>

</p>

</div>

<div class="container">

<div class="mb-4">

<a href="export_pdf.php" class="btn btn-danger">

Export PDF

</a>

<a href="export_excel.php" class="btn btn-success">

Export Excel

</a>

<button onclick="window.print()" class="btn btn-secondary">

Print

</button>

</div>

</div>

<div class="container mt-4">

<div class="row">

<div class="col-lg col-md-4 col-sm-6 mb-3">

<div class="card h-100 text-white bg-primary shadow">

<div class="card-body text-center">

<h6>Total Requests</h6>

<h2 class="fw-bold">

<?= number_format($stats['total_requests'] ?? 0); ?>

</h2>

</div>

</div>

</div>

<div class="col-lg col-md-4 col-sm-6 mb-3">

<div class="card h-100 text-white bg-warning shadow">

<div class="card-body text-center">

<h6>Pending</h6>

<h2 class="fw-bold">

<?= number_format($stats['pending'] ?? 0); ?>

</h2>

</div>

</div>

</div>

<div class="col-lg col-md-4 col-sm-6 mb-3">

<div class="card h-100 text-white bg-secondary shadow">

<div class="card-body text-center">

<h6>Assigned</h6>

<h2 class="fw-bold">

<?= number_format($stats['assigned'] ?? 0); ?>

</h2>

</div>

</div>

</div>

<div class="col-lg col-md-4 col-sm-6 mb-3">

<div class="card h-100 text-white bg-info shadow">

<div class="card-body text-center">

<h6>In Progress</h6>

<h2 class="fw-bold">

<?= number_format($stats['in_progress'] ?? 0); ?>

</h2>

</div>

</div>

</div>

<div class="col-lg col-md-4 col-sm-6 mb-3">

<div class="card h-100 text-white bg-success shadow">

<div class="card-body text-center">

<h6>Completed</h6>

<h2 class="fw-bold">

<?= number_format($stats['completed'] ?? 0); ?>

</h2>

</div>

</div>

</div>

<div class="col-lg col-md-4 col-sm-6 mb-3">

<div class="card h-100 text-white bg-danger shadow">

<div class="card-body text-center">

<h6>Cancelled</h6>

<h2 class="fw-bold">

<?= number_format($stats['cancelled'] ?? 0); ?>

</h2>

</div>

</div>

</div>

</div>

</div>

<div class="container mt-4">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

Requests by Category

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-striped table-bordered table-hover">

<thead>

<tr>

<th>Category</th>

<th>Total Requests</th>

</tr>

</thead>

<tbody>

<?php if (!empty($categories)) : ?>

<?php foreach ($categories as $category): ?>

<tr>

<td>

<?= htmlspecialchars($category['category_name']); ?>

</td>

<td>

<?= number_format($category['total']); ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="2"
class="text-center text-muted">

No category data available.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<div class="container mt-4 mb-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

Recent Maintenance Requests

</h5>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-striped table-bordered table-hover">

<thead>

<tr>

<th>Ticket</th>

<th>Requester</th>

<th>Category</th>

<th>Title</th>

<th>Status</th>

<th>Priority</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php if (!empty($recentRequests)) : ?>

<?php foreach($recentRequests as $request): ?>

<tr>

<td>

<?= htmlspecialchars($request['ticket_number']); ?>

</td>

<td>

<?= htmlspecialchars($request['fullname']); ?>

</td>

<td>

<?= htmlspecialchars($request['category_name']); ?>

</td>

<td>

<?= htmlspecialchars($request['title']); ?>

</td>

<td>

<?php

switch ($request['status']) {

    case 'Pending':
        $statusBadge = 'warning';
        break;

    case 'Assigned':
        $statusBadge = 'primary';
        break;

    case 'In Progress':
        $statusBadge = 'info';
        break;

    case 'Completed':
        $statusBadge = 'success';
        break;

    case 'Cancelled':
        $statusBadge = 'danger';
        break;

    default:
        $statusBadge = 'secondary';

}

?>

<span class="badge bg-<?= $statusBadge; ?>">

<?= htmlspecialchars($request['status']); ?>

</span>

</td>

<td>

<?php

switch ($request['priority']) {

    case 'Low':
        $priorityBadge = 'success';
        break;

    case 'Medium':
        $priorityBadge = 'warning';
        break;

    case 'High':
        $priorityBadge = 'danger';
        break;

    default:
        $priorityBadge = 'secondary';

}

?>

<span class="badge bg-<?= $priorityBadge; ?>">

<?= htmlspecialchars($request['priority']); ?>

</span>

</td>

<td>

<?= date("d M Y", strtotime($request['created_at'])); ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="7"
class="text-center text-muted">

No recent activity found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<div class="container mb-5">

<a
href="dashboard.php"
class="btn btn-secondary">

← Back to Dashboard

</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>