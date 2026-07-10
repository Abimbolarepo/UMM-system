<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Maintenance Officer");

/*
|--------------------------------------------------------------------------
| Validate Request
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid maintenance request.";

    header("Location: my_jobs.php");

    exit;
}

$requestId = (int) $_GET['id'];

$officerId = $_SESSION['user_id'];

$assignmentModel = new Assignment();

/*
|--------------------------------------------------------------------------
| Retrieve Job
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
| Only In Progress Jobs Can Be Completed
|--------------------------------------------------------------------------
*/

if ($job['request_status'] !== "In Progress") {

    $_SESSION['error'] = "Only jobs that are In Progress can be completed.";

    header("Location: view_job.php?id=" . $requestId);

    exit;
}

/*
|--------------------------------------------------------------------------
| Complete Job
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $remarks = trim($_POST['remarks']);

    if (empty($remarks)) {

        $_SESSION['error'] = "Completion remarks are required.";

    } else {

        if ($assignmentModel->completeJob(
            $requestId,
            $officerId,
            $remarks
        )) {

            $_SESSION['success'] =
                "Maintenance job completed successfully.";

            header("Location: view_job.php?id=" . $requestId);

            exit;

        } else {

            $_SESSION['error'] =
                "Unable to complete maintenance job.";

        }

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Complete Job | UMMS</title>

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

            <a
                href="../../app/controllers/AuthController.php?action=logout"
                class="btn btn-danger btn-sm ms-3">

                Logout

            </a>

        </div>

    </div>

</nav>

<div class="container mt-4">

    <?php if(isset($_SESSION['success'])): ?>

        <div class="alert alert-success">

            <?= $_SESSION['success']; ?>

        </div>

        <?php unset($_SESSION['success']); ?>

    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>

        <div class="alert alert-danger">

            <?= $_SESSION['error']; ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <div class="card shadow">

        <div class="card-header bg-success text-white">

            <h3 class="mb-0">

                Complete Maintenance Job

            </h3>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>

                    <th width="220">

                        Ticket Number

                    </th>

                    <td>

                        <?= htmlspecialchars($job['ticket_number']); ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Title

                    </th>

                    <td>

                        <?= htmlspecialchars($job['title']); ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Student

                    </th>

                    <td>

                        <?= htmlspecialchars($job['student_name']); ?>

                    </td>

                </tr>

                <tr>

                    <th>

                        Current Status

                    </th>

                    <td>

                        <span class="badge bg-info">

                            <?= htmlspecialchars($job['request_status']); ?>

                        </span>

                    </td>

                </tr>

            </table>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">

                        Completion Remarks

                    </label>

                    <textarea
                        name="remarks"
                        rows="6"
                        class="form-control"
                        required
                        placeholder="Describe the work completed..."><?= isset($_POST['remarks']) ? htmlspecialchars($_POST['remarks']) : ""; ?></textarea>

                </div>

                <div class="d-flex justify-content-between">

                    <a
                        href="view_job.php?id=<?= $requestId; ?>"
                        class="btn btn-secondary">

                        ← Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-miva"
                        onclick="return confirm('Mark this maintenance job as completed?');">

                        ✔ Complete Job

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>