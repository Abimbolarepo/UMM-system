<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

/*
|--------------------------------------------------------------------------
| Validate Request ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid maintenance request.";
    header("Location: manage_requests.php");
    exit;
}

$requestId = (int) $_GET['id'];

$pageTitle = "Update Request Status";

$serviceRequest = new ServiceRequest();

/*
|--------------------------------------------------------------------------
| Update Request Status
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status = trim($_POST['status']);

    $allowedStatuses = [
        "Pending",
        "Assigned",
        "In Progress",
        "Completed",
        "Cancelled"
    ];

    if (!in_array($status, $allowedStatuses, true)) {

        $_SESSION['error'] = "Invalid status selected.";

    } else {

        if ($serviceRequest->updateStatus($requestId, $status)) {

            $_SESSION['success'] = "Maintenance request status updated successfully.";

            header("Location: manage_requests.php");
            exit;

        } else {

            $_SESSION['error'] = "Failed to update maintenance request.";

        }

    }

}

/*
|--------------------------------------------------------------------------
| Load Maintenance Request
|--------------------------------------------------------------------------
*/

$request = $serviceRequest->getRequestDetails($requestId);

if (!$request) {
    $_SESSION['error'] = "Maintenance request not found.";
    header("Location: manage_requests.php");
    exit;
}

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

    <a href="manage_requests.php"
       class="btn btn-secondary mb-4">
        ← Back to Requests
    </a>

    <div class="card shadow">

        <div class="card-header bg-warning">

            <h4 class="mb-0">
                Update Maintenance Request Status
            </h4>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="220">Ticket Number</th>
                    <td><?= htmlspecialchars($request['ticket_number']); ?></td>
                </tr>

                <tr>
                    <th>Student</th>
                    <td><?= htmlspecialchars($request['fullname']); ?></td>
                </tr>

                <tr>
                    <th>Title</th>
                    <td><?= htmlspecialchars($request['title']); ?></td>
                </tr>

                <tr>
                    <th>Current Status</th>
                    <td>
                        <?php

                        $statusBadge = match ($request['status'] ?? '') {
                            'Pending'     => 'secondary',
                            'Assigned'    => 'warning',
                            'In Progress' => 'info',
                            'Completed'   => 'success',
                            'Cancelled'   => 'dark',
                            default       => 'secondary'
                        };

                        ?>

                        <span class="badge bg-<?= $statusBadge; ?>">
                            <?= htmlspecialchars($request['status']); ?>
                        </span>
                    </td>
                </tr>

            </table>

            <hr>

            <form method="POST">

                <div class="mb-3">

                    <label for="status" class="form-label fw-bold">
                        Change Status
                    </label>

                    <select
                        name="status"
                        id="status"
                        class="form-select"
                        required>

                        <option value="">-- Select Status --</option>

                        <option value="Pending"
                            <?= ($request['status'] == 'Pending') ? 'selected' : ''; ?>>
                            Pending
                        </option>

                        <option value="Assigned"
                            <?= ($request['status'] == 'Assigned') ? 'selected' : ''; ?>>
                            Assigned
                        </option>

                        <option value="In Progress"
                            <?= ($request['status'] == 'In Progress') ? 'selected' : ''; ?>>
                            In Progress
                        </option>

                        <option value="Completed"
                            <?= ($request['status'] == 'Completed') ? 'selected' : ''; ?>>
                            Completed
                        </option>

                        <option value="Cancelled"
                            <?= ($request['status'] == 'Cancelled') ? 'selected' : ''; ?>>
                            Cancelled
                        </option>

                    </select>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    Update Status

                </button>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>