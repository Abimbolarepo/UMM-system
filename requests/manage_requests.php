<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$serviceRequest = new ServiceRequest();

function getPriorityBadge($priority)
{
    return match ($priority) {
        "Critical" => "danger",
        "High"     => "warning",
        "Medium"   => "primary",
        default    => "secondary"
    };
}

function getStatusBadge($status)
{
    return match ($status) {
        "Pending"     => "secondary",
        "Assigned"    => "warning",
        "In Progress" => "info",
        "Completed"   => "success",
        "Cancelled"   => "dark",
        default       => "secondary"
    };
}

/*
|--------------------------------------------------------------------------
| Search & Filter
|--------------------------------------------------------------------------
*/

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

if (!empty($search)) {

    $requests = $serviceRequest->searchRequests($search);

} elseif (!empty($status)) {

    $requests = $serviceRequest->getRequestsByStatus($status);

} else {

    $requests = $serviceRequest->getAllRequests();

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Manage Requests | UMMS</title>

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

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>

<?php if (isset($_SESSION['error'])) : ?>

    <div class="alert alert-danger alert-dismissible fade show">

        <?= htmlspecialchars($_SESSION['error']); ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">

                Manage Maintenance Requests

            </h2>

            <p class="text-muted">

                View, search and assign maintenance requests.

            </p>

        </div>

        <a href="dashboard.php"
           class="btn btn-secondary">

            ← Dashboard

        </a>

    </div>
<div class="card shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-6 mb-2">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search Ticket, Student or Category..."
                        value="<?= htmlspecialchars($search); ?>">

                </div>

                <div class="col-md-4 mb-2">

                    <select
                        name="status"
                        class="form-select">

                        <option value="" <?= $status == "" ? "selected" : ""; ?>>All Status</option>

                        <option value="Pending" <?= $status == "Pending" ? "selected" : ""; ?>>Pending</option>

                        <option value="Assigned" <?= $status == "Assigned" ? "selected" : ""; ?>>Assigned</option>

                        <option value="In Progress" <?= $status == "In Progress" ? "selected" : ""; ?>>In Progress</option>

                        <option value="Completed" <?= $status == "Completed" ? "selected" : ""; ?>>Completed</option>

                        <option value="Cancelled" <?= $status == "Cancelled" ? "selected" : ""; ?>>Cancelled</option>

                    </select>

                </div>

                <div class="col-md-2 d-grid gap-2">

                    <button
                        class="btn btn-primary"
                        type="submit">

                        Search

                    </button>

                    <a href="manage_requests.php"
                       class="btn btn-outline-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="card shadow-sm">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>
                        <th>Ticket Number</th>
                        <th>Student</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Assigned Officer</th>
                        <th>Status</th>
                        <th>Date Submitted</th>
                        <th class="text-center">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (!empty($requests)) : ?>

                        <?php foreach ($requests as $r) : ?>

                            <?php

                            $priorityBadge = getPriorityBadge($r['priority'] ?? '');
                            $statusBadge   = getStatusBadge($r['status'] ?? '');

                            ?>

                            <tr>
                                <td><?= htmlspecialchars($r['ticket_number']); ?></td>
                                <td><?= htmlspecialchars($r['fullname']); ?></td>
                                <td><?= htmlspecialchars($r['category_name']); ?></td>
                                <td><?= htmlspecialchars($r['title']); ?></td>
                                <td>
                                    <span class="badge bg-<?= $priorityBadge; ?>">
                                        <?= htmlspecialchars($r['priority']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($r['assigned_officer'])) : ?>
                                        <?= htmlspecialchars($r['assigned_officer']); ?>
                                    <?php else : ?>
                                        <span class="text-muted">Not Assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusBadge; ?>">
                                        <?= htmlspecialchars($r['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date("d M Y h:i A", strtotime($r['created_at'])); ?>
                                </td>
                                <td class="text-center">
                                    <a href="view_request.php?id=<?= (int) $r['request_id']; ?>"
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>

                                    <?php if (in_array($r['status'], ['Pending', 'Assigned'])) : ?>

                                    <a href="assign_request.php?id=<?= (int) $r['request_id']; ?>"
                                       class="btn btn-sm btn-success">
                                        Assign
                                    </a>

                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No maintenance requests found.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>