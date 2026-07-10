<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$requestModel = new ServiceRequest();

/*
|--------------------------------------------------------------------------
| Search & Filter
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

if (!empty($search)) {

    $requests = $requestModel->searchRequests($search);

} elseif (!empty($status)) {

    $requests = $requestModel->getRequestsByStatus($status);

} else {

    $requests = $requestModel->getAllRequests();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manage Maintenance Requests | UMMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../../assets/css/style.css">

</head>

<body class="bg-light">

<!-- Navigation -->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">

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

<!-- Main Content -->

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3 class="mb-0">

                Manage Maintenance Requests

            </h3>

        </div>

        <div class="card-body">

            <!-- Search & Filter -->

            <form method="GET" class="row g-3 mb-4">

                <div class="col-md-6">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search Ticket, Student, Category or Title"
                        value="<?= htmlspecialchars($search); ?>">

                </div>

                <div class="col-md-3">

                    <select
                        name="status"
                        class="form-select">

                        <option value="">All Status</option>

                        <option value="Pending"
                            <?= ($status == "Pending") ? "selected" : ""; ?>>

                            Pending

                        </option>

                        <option value="Assigned"
                            <?= ($status == "Assigned") ? "selected" : ""; ?>>

                            Assigned

                        </option>

                        <option value="In Progress"
                            <?= ($status == "In Progress") ? "selected" : ""; ?>>

                            In Progress

                        </option>

                        <option value="Completed"
                            <?= ($status == "Completed") ? "selected" : ""; ?>>

                            Completed

                        </option>

                        <option value="Cancelled"
                            <?= ($status == "Cancelled") ? "selected" : ""; ?>>

                            Cancelled

                        </option>

                    </select>

                </div>

                <div class="col-md-3 d-grid gap-2 d-md-flex">

                    <button
                        class="btn btn-primary flex-fill">

                        Search

                    </button>

                    <a href="manage_requests.php"
                       class="btn btn-secondary flex-fill">

                        Reset

                    </a>

                </div>

            </form>

            <!-- Requests Table -->

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle">

                    <thead class="table-dark">

                    <tr>

                        <th>Ticket</th>

                        <th>Student</th>

                        <th>Category</th>

                        <th>Title</th>

                        <th>Priority</th>

                        <th>Status</th>

                        <th>Date Submitted</th>

                        <th width="250">Actions</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if (!empty($requests)): ?>

                        <?php foreach ($requests as $row): ?>

                            <?php

                            // Priority Badge

                            switch ($row['priority']) {

                                case "Critical":
                                    $priorityBadge = "danger";
                                    break;

                                case "High":
                                    $priorityBadge = "warning";
                                    break;

                                case "Medium":
                                    $priorityBadge = "primary";
                                    break;

                                default:
                                    $priorityBadge = "secondary";

                            }

                            // Status Badge

                            switch ($row['status']) {

                                case "Pending":
                                    $statusBadge = "warning";
                                    break;

                                case "Assigned":
                                    $statusBadge = "info";
                                    break;

                                case "In Progress":
                                    $statusBadge = "primary";
                                    break;

                                case "Completed":
                                    $statusBadge = "success";
                                    break;

                                case "Cancelled":
                                    $statusBadge = "danger";
                                    break;

                                default:
                                    $statusBadge = "secondary";

                            }

                            ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($row['ticket_number']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($row['fullname']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($row['category_name']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($row['title']); ?>

                                </td>

                                <td>

                                    <span class="badge bg-<?= $priorityBadge; ?>">

                                        <?= htmlspecialchars($row['priority']); ?>

                                    </span>

                                </td>

                                <td>

                                    <span class="badge bg-<?= $statusBadge; ?>">

                                        <?= htmlspecialchars($row['status']); ?>

                                    </span>

                                </td>

                                <td>

                                    <?= date("d M Y", strtotime($row['created_at'])); ?>

                                </td>

                                <td>

                                    <a href="view_request.php?id=<?= $row['request_id']; ?>"
                                       class="btn btn-primary btn-sm">

                                        View

                                    </a>

                                    <a href="assign_request.php?id=<?= $row['request_id']; ?>"
                                       class="btn btn-success btn-sm">

                                        Assign

                                    </a>

                                    <a href="update_status.php?id=<?= $row['request_id']; ?>"
                                       class="btn btn-warning btn-sm">

                                        Status

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8" class="text-center text-muted py-4">

                                No maintenance requests found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <hr>

            <div class="d-flex justify-content-between">

                <a href="dashboard.php"
                   class="btn btn-secondary">

                    ← Back to Dashboard

                </a>

                <span class="text-muted">

                    Total Requests:
                    <strong><?= count($requests); ?></strong>

                </span>

            </div>

        </div>

    </div>

</div>

</body>

</html>