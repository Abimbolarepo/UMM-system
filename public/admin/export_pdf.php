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
$requests = $requestModel->getRecentActivities(100);

?>
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>UMMS Report</title>

<style>

body{
    font-family:Arial, sans-serif;
    font-size:13px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
}

table th,
table td{
    border:1px solid #000;
    padding:8px;
}

h1,h2{
    margin-bottom:5px;
}

</style>

</head>

<body onload="window.print()">

<h1>University Maintenance Management System</h1>

<h2>Maintenance Report</h2>

<p>

Generated:

<?= date("d M Y h:i A"); ?>

</p>

<hr>

<h3>Summary</h3>

<table>

<tr>

<th>Total</th>

<th>Pending</th>

<th>Assigned</th>

<th>In Progress</th>

<th>Completed</th>

<th>Cancelled</th>

</tr>

<tr>

<td><?= $stats['total_requests']; ?></td>

<td><?= $stats['pending']; ?></td>

<td><?= $stats['assigned']; ?></td>

<td><?= $stats['in_progress']; ?></td>

<td><?= $stats['completed']; ?></td>

<td><?= $stats['cancelled']; ?></td>

</tr>

</table>

<h3>Requests by Category</h3>

<table>

<tr>

<th>Category</th>

<th>Total</th>

</tr>

<?php foreach($categories as $category): ?>

<tr>

<td><?= htmlspecialchars($category['category_name']); ?></td>

<td><?= $category['total']; ?></td>

</tr>

<?php endforeach; ?>

</table>

<h3>Recent Requests</h3>

<table>

<tr>

<th>Ticket</th>

<th>Requester</th>

<th>Status</th>

<th>Priority</th>

<th>Date</th>

</tr>

<?php foreach($requests as $request): ?>

<tr>

<td><?= htmlspecialchars($request['ticket_number']); ?></td>

<td><?= htmlspecialchars($request['fullname']); ?></td>

<td><?= htmlspecialchars($request['status']); ?></td>

<td><?= htmlspecialchars($request['priority']); ?></td>

<td><?= date("d M Y",strtotime($request['created_at'])); ?></td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>