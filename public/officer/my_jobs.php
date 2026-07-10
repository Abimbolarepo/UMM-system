<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Maintenance Officer");

$assignment = new Assignment();

/*
|--------------------------------------------------------------------------
| Load Assigned Jobs
|--------------------------------------------------------------------------
*/

$jobs = $assignment->getOfficerAssignments($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Assigned Jobs | UMMS</title>

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

        <div class="ms-auto">

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

                My Assigned Maintenance Jobs

            </h3>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                    <tr>

                        <th>Ticket</th>

                        <th>Student</th>

                        <th>Category</th>

                        <th>Priority</th>

                        <th>Status</th>

                        <th>Assigned Date</th>

                        <th width="120">Action</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if (!empty($jobs)): ?>

                        <?php foreach ($jobs as $job): ?>

                            <tr>

                                <td>

                                    <?= htmlspecialchars($job['ticket_number']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($job['student_name']); ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars($job['category_name']); ?>

                                </td>

                                <td>

                                    <?php

                                    switch ($job['priority']) {

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

                                    ?>

                                    <span class="badge bg-<?= $priorityBadge; ?>">

                                        <?= htmlspecialchars($job['priority']); ?>

                                    </span>

                                </td>

                                <td>

                                    <?php

                                    switch ($job['request_status']) {

                                        case "Assigned":
                                            $statusBadge = "warning";
                                            break;

                                        case "In Progress":
                                            $statusBadge = "primary";
                                            break;

                                        case "Completed":
                                            $statusBadge = "success";
                                            break;

                                        case "Cancelled":
                                            $statusBadge = "dark";
                                            break;

                                        default:
                                            $statusBadge = "secondary";
                                    }

                                    ?>

                                    <span class="badge bg-<?= $statusBadge; ?>">

                                        <?= htmlspecialchars($job['request_status']); ?>

                                    </span>

                                </td>

                                <td>

                                    <?= date("d M Y", strtotime($job['assigned_at'])); ?>

                                </td>

                                <td>

                                    <a href="view_job.php?id=<?= (int) $job['request_id']; ?>"
                                       class="btn btn-primary btn-sm">

                                        View

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7" class="text-center text-muted">

                                No maintenance jobs have been assigned to you.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                <a href="dashboard.php"
                   class="btn btn-secondary">

                    ← Back to Dashboard

                </a>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>