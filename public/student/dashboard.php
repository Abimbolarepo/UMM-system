<?php
session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();

/*
|--------------------------------------------------------------------------
| Load Student Statistics
|--------------------------------------------------------------------------
*/

$requestModel = new ServiceRequest();

$stats = $requestModel->getStudentStatistics($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student Dashboard | UMMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../assets/css/style.css">

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

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2 class="mb-3">

                Welcome,
                <?= htmlspecialchars($_SESSION['firstname']); ?> 👋

            </h2>

            <p class="text-muted">

                Manage your maintenance requests from one place.

            </p>

            <hr>

            <p>

                Welcome to the <strong>University Maintenance Management System (UMMS)</strong>.

                From here, you can submit maintenance requests, monitor their progress,
                and view the status of all your reported issues in real time.

            </p>

            <!-- Success Message -->

            <?php if(isset($_SESSION['success'])): ?>

                <div class="alert alert-success">

                    <?= $_SESSION['success']; ?>

                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <!-- Error Message -->

            <?php if(isset($_SESSION['error'])): ?>

                <div class="alert alert-danger">

                    <?= $_SESSION['error']; ?>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <!-- Statistics -->

            <div class="row mt-4">

                <div class="col-md-3 mb-3">

                    <div class="card border-warning shadow-sm text-center">

                        <div class="card-body">

                            <h5>Pending</h5>

                            <h2><?= $stats['pending']; ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-info shadow-sm text-center">

                        <div class="card-body">

                            <h5>Assigned</h5>

                            <h2><?= $stats['assigned']; ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-primary shadow-sm text-center">

                        <div class="card-body">

                            <h5>In Progress</h5>

                            <h2><?= $stats['in_progress']; ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-success shadow-sm text-center">

                        <div class="card-body">

                            <h5>Completed</h5>

                            <h2><?= $stats['completed']; ?></h2>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Action Cards -->

            <div class="row mt-4">

                <div class="col-md-6 mb-3">

                    <div class="card border-primary shadow-sm">

                        <div class="card-body text-center">

                            <h4>📝 Submit Request</h4>

                            <p>

                                Report a maintenance issue in your hostel,
                                classroom, laboratory or office.

                            </p>

                            <a href="submit_request.php"
                               class="btn btn-primary">

                                Submit Maintenance Request

                            </a>

                        </div>

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <div class="card border-success shadow-sm">

                        <div class="card-body text-center">

                            <h4>📋 My Requests</h4>

                            <p>

                                View all maintenance requests you have submitted
                                and monitor their status.

                            </p>

                            <a href="my_requests.php"
                               class="btn btn-success">

                                View My Requests

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>