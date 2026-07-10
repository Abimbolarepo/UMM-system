<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$pageTitle = "Maintenance History";

$assignment = new Assignment();

$jobs = $assignment->getCompletedJobs();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $pageTitle; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow">

<div class="container">

<span class="navbar-brand fw-bold">

University Maintenance Management System

</span>

<div>

Welcome,

<strong><?= htmlspecialchars($_SESSION['firstname']); ?></strong>

<a href="../../app/controllers/AuthController.php?action=logout"
class="btn btn-danger btn-sm ms-3">

Logout

</a>

</div>

</div>

</nav>

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-secondary mb-3">

← Back to Dashboard

</a>

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3 class="mb-0">

Completed Maintenance Jobs

</h3>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>Ticket</th>

<th>Student</th>

<th>Officer</th>

<th>Category</th>

<th>Title</th>

<th>Location</th>

<th>Completed</th>

<th>Remarks</th>

</tr>

</thead>

<tbody>

<?php if (!empty($jobs)): ?>

<?php foreach ($jobs as $job): ?>

<tr>

<td><?= htmlspecialchars($job['ticket_number']); ?></td>

<td><?= htmlspecialchars($job['student_name']); ?></td>

<td><?= htmlspecialchars($job['officer_name']); ?></td>

<td><?= htmlspecialchars($job['category_name']); ?></td>

<td><?= htmlspecialchars($job['title']); ?></td>

<td><?= htmlspecialchars($job['location']); ?></td>

<td><?= date("d M Y H:i", strtotime($job['completed_at'])); ?></td>

<td><?= htmlspecialchars($job['remarks'] ?? '-'); ?></td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="8" class="text-center text-muted">

No completed maintenance jobs found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>

</html>