<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Maintenance Officer");

/*
|--------------------------------------------------------------------------
| Validate Request ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid maintenance request selected.";
    header("Location: my_jobs.php");
    exit;
}

$requestId = (int) $_GET['id'];
$officerId = $_SESSION['user_id'];

$assignmentModel = new Assignment();

/*
|--------------------------------------------------------------------------
| Retrieve Job Details
|--------------------------------------------------------------------------
*/

$job = $assignmentModel->getOfficerJob($requestId, $officerId);

if (!$job) {
    $_SESSION['error'] = "Maintenance request not found.";
    header("Location: my_jobs.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Priority Badge
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

switch ($job['request_status']) {

    case "Assigned":
        $statusBadge = "warning";
        break;

    case "In Progress":
        $statusBadge = "info";
        break;

    case "Completed":
        $statusBadge = "success";
        break;

    default:
        $statusBadge = "secondary";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>View Job | UMMS</title>

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

        <?php if (isset($_SESSION['success'])) : ?>

            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>

            <div class="alert alert-danger">
                <?= $_SESSION['error']; ?>
            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                <h3 class="mb-0">
                    Maintenance Job Details
                </h3>

            </div>

            <div class="card-body">

                <div class="row">

                    <!-- Maintenance Request Details -->

                    <div class="col-lg-8">

                        <div class="card border-0 shadow-sm mb-4">

                            <div class="card-header bg-light">

                                <h5 class="mb-0">
                                    Maintenance Request Details
                                </h5>

                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>
                                        <th width="220">Ticket Number</th>
                                        <td><?= htmlspecialchars($job['ticket_number']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Category</th>
                                        <td><?= htmlspecialchars($job['category_name']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Title</th>
                                        <td><?= htmlspecialchars($job['title']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Description</th>
                                        <td style="white-space: pre-line;">
                                            <?= htmlspecialchars($job['description']); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Building</th>
                                        <td><?= htmlspecialchars($job['building']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Room Number</th>
                                        <td><?= htmlspecialchars($job['room_number']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Location</th>
                                        <td><?= htmlspecialchars($job['location']); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Priority</th>
                                        <td>
                                            <span class="badge bg-<?= $priorityBadge; ?>">
                                                <?= htmlspecialchars($job['priority']); ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <span class="badge bg-<?= $statusBadge; ?>">
                                                <?= htmlspecialchars($job['request_status']); ?>
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Date Submitted</th>
                                        <td>
                                            <?= date("d M Y h:i A", strtotime($job['created_at'])); ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Date Assigned</th>
                                        <td>
                                            <?= date("d M Y h:i A", strtotime($job['assignment_assigned_at'])); ?>
                                        </td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- Student Information -->

                    <div class="col-lg-4">

                        <div class="card border-0 shadow-sm mb-4">

                            <div class="card-header bg-light">

                                <h5 class="mb-0">
                                    Student Information
                                </h5>

                            </div>

                            <div class="card-body">

                                <p>
                                    <strong>Name:</strong><br>
                                    <?= htmlspecialchars($job['student_name']); ?>
                                </p>

                                <p>
                                    <strong>Email:</strong><br>
                                    <?= htmlspecialchars($job['email']); ?>
                                </p>

                                <p>
                                    <strong>Phone:</strong><br>
                                    <?= htmlspecialchars($job['phone']); ?>
                                </p>

                            </div>

                        </div>

                        <div class="card border-0 shadow-sm">

                            <div class="card-header bg-light">

                                <h5 class="mb-0">
                                    Uploaded Image
                                </h5>

                            </div>

                            <div class="card-body text-center">

                                <?php if (!empty($job['image'])) : ?>

                                    <img
                                        src="../../uploads/requests/<?= htmlspecialchars(basename($job['image'])); ?>"
                                        alt="Maintenance Request"
                                        class="img-fluid rounded shadow">

                                <?php else : ?>

                                    <p class="text-muted mb-0">
                                        No image uploaded.
                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- PART 3 STARTS HERE -->

                <div class="row mt-4">

                    <div class="col-md-12">

                        <div class="card border-0 shadow-sm">

                            <div class="card-header bg-light">

                                <h5 class="mb-0">
                                    Assignment Information
                                </h5>

                            </div>

                            <div class="card-body">

                                <table class="table table-bordered">

                                    <tr>

                                        <th width="220">Assignment Status</th>

                                        <td>

                                            <span class="badge bg-info">

                                                <?= htmlspecialchars($job['assignment_status']); ?>

                                            </span>

                                        </td>

                                    </tr>

                                    <tr>

                                        <th>Officer Remarks</th>

                                        <td>

                                            <?=
                                                !empty($job['remarks'])
                                                    ? nl2br(htmlspecialchars($job['remarks']))
                                                    : "<span class='text-muted'>No remarks available.</span>";
                                            ?>

                                        </td>

                                    </tr>

                                    <?php if (!empty($job['completed_at'])) : ?>

                                        <tr>

                                            <th>Date Completed</th>

                                            <td>

                                                <?= date("d M Y h:i A", strtotime($job['completed_at'])); ?>

                                            </td>

                                        </tr>

                                    <?php endif; ?>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">

                    <a href="my_jobs.php"
                       class="btn btn-secondary">

                        ← Back to My Jobs

                    </a>

                    <div>

                        <?php if ($job['request_status'] == "Assigned") : ?>

                            <a href="in_progress.php?id=<?= (int) $job['request_id']; ?>"
                               class="btn btn-primary"
                               onclick="return confirm('Start this maintenance job?');">

                                ▶ Start Job

                            </a>

                        <?php elseif ($job['request_status'] == "In Progress") : ?>

                            <a href="complete_job.php?id=<?= (int) $job['request_id']; ?>"
                               class="btn btn-success">

                                ✔ Complete Job

                            </a>

                        <?php elseif ($job['request_status'] == "Completed") : ?>

                            <button class="btn btn-success" disabled>

                                ✔ Job Completed

                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>