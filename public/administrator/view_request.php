<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid maintenance request.";
    header("Location: manage_requests.php");
    exit;
}

$requestId = (int) $_GET['id'];

$pageTitle      = "View Maintenance Request";

$serviceRequest = new ServiceRequest();
$assignment     = new Assignment();

/*
|--------------------------------------------------------------------------
| Load Request
|--------------------------------------------------------------------------
*/

$request = $serviceRequest->getRequestDetails($requestId);

if (!$request) {
    $_SESSION['error'] = "Maintenance request not found.";
    header("Location: manage_requests.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Assignment Information
|--------------------------------------------------------------------------
*/

$assignmentInfo = $assignment->getAssignment($requestId);
$assigned       = $assignmentInfo !== false;

/*
|--------------------------------------------------------------------------
| Maintenance Officers
|--------------------------------------------------------------------------
*/

$officers = $serviceRequest->getMaintenanceOfficers();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title><?= htmlspecialchars($pageTitle); ?> | UMMS</title>

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

            <div class="alert alert-success alert-dismissible fade show">

                <?= htmlspecialchars($_SESSION['success']); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>

            <div class="alert alert-danger alert-dismissible fade show">

                <?= htmlspecialchars($_SESSION['error']); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>

        <a href="manage_requests.php"
           class="btn btn-secondary mb-4">
            ← Back to Requests
        </a>

        <h2 class="fw-bold">
            Maintenance Request Details
        </h2>

        <p class="text-muted">
            Review the maintenance request and assign an officer.
        </p>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    Request Information
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <strong>Ticket Number</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['ticket_number']); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Student</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['fullname'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Category</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['category_name'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Priority</strong>

                        <p class="mb-0">

                            <span class="badge bg-<?=
                                match ($request['priority'] ?? '') {
                                    'Critical' => 'danger',
                                    'High'     => 'warning',
                                    'Medium'   => 'primary',
                                    default    => 'secondary'
                                };
                            ?>">

                                <?= htmlspecialchars($request['priority']); ?>

                            </span>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Status</strong>

                        <p class="mb-0">

                            <span class="badge bg-<?=
                                match ($request['status'] ?? '') {
                                    'Pending'     => 'secondary',
                                    'Assigned'    => 'warning',
                                    'In Progress' => 'info',
                                    'Completed'   => 'success',
                                    'Cancelled'   => 'dark',
                                    default       => 'secondary'
                                };
                            ?>">

                                <?= htmlspecialchars($request['status']); ?>

                            </span>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Date Submitted</strong>

                        <p class="mb-0">

                            <?= !empty($request['created_at'])
                            ? date("d M Y h:i A", strtotime($request['created_at']))
                            : "N/A"; ?>

                        </p>

                    </div>

                    <div class="col-12 mb-3">

                        <strong>Title</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['title'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-12 mb-3">

                        <strong>Description</strong>

                        <p class="mb-0">

                            <?= nl2br(htmlspecialchars($request['description'] ?? 'No description provided.')); ?>

                        </p>

                    </div>

                    <div class="col-md-4 mb-3">

                        <strong>Location</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['location'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-4 mb-3">

                        <strong>Building</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['building'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-4 mb-3">

                        <strong>Room Number</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['room_number'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Email</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['email'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Phone</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($request['phone'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <?php
                    $imagePath = "../../uploads/" . basename($request['image'] ?? '');

                    if (!empty($request['image']) && file_exists($imagePath)) : ?>

                        <div class="col-12 mb-3">

                            <strong>Uploaded Image</strong>

                            <div class="mt-2">

                                <img
                                    src="<?= htmlspecialchars($imagePath); ?>"
                                    alt="Maintenance Request Image"
                                    class="img-fluid rounded border shadow-sm"
                                    style="max-width:400px;">

                            </div>

                        </div>

                    <?php elseif (!empty($request['image'])) : ?>

                        <div class="col-12 mb-3">

                            <strong>Uploaded Image</strong>

                            <p class="text-danger mb-0">
                                Image file could not be found.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <?php if ($assigned) : ?>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">
                    Assignment Information
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <strong>Assigned Officer</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($assignmentInfo['officer_name'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Assigned Date</strong>

                        <p class="mb-0">

                            <?= !empty($assignmentInfo['assigned_at'])
                            ? date("d M Y h:i A", strtotime($assignmentInfo['assigned_at']))
                            : 'N/A'; ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Email</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($assignmentInfo['email'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Phone</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($assignmentInfo['phone'] ?? 'N/A'); ?>

                        </p>

                    </div>

                    <div class="col-md-6 mb-3">

                        <strong>Department</strong>

                        <p class="mb-0">

                            <?= htmlspecialchars($assignmentInfo['department'] ?? 'N/A'); ?>

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <?php endif; ?>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-warning">

                <h5 class="mb-0">
                    Assign Maintenance Officer
                </h5>

            </div>

            <div class="card-body">

                <?php if ($assigned): ?>

                    <div class="alert alert-success mb-0">

                        <strong>
                            This request has already been assigned.
                        </strong>

                    </div>

                <?php elseif (empty($officers)): ?>

                    <div class="alert alert-warning mb-0">

                        No maintenance officers are available.

                    </div>

                <?php else: ?>

                    <form action="../../app/controllers/AssignmentController.php"
                          method="POST">

                        <input
                            type="hidden"
                            name="request_id"
                            value="<?= (int)$requestId; ?>">

                        <div class="mb-3">

                            <label class="form-label">
                                Select Maintenance Officer
                            </label>

                            <select
                                name="officer_id"
                                class="form-select"
                                required>

                                <option value="">
                                    -- Select Officer --
                                </option>

                                <?php foreach ($officers as $officer): ?>

                                    <option value="<?= (int)$officer['user_id']; ?>">

                                        <?= htmlspecialchars($officer['fullname']); ?>

                                        (<?= htmlspecialchars($officer['department']); ?>)

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Assignment Notes (Optional)
                            </label>

                            <textarea
                                name="notes"
                                class="form-control"
                                rows="4"
                                placeholder="Additional instructions..."></textarea>

                        </div>

                        <button
                            type="submit"
                            name="assign_request"
                            class="btn btn-success">

                            Assign Request

                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>