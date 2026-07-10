<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Maintenance Officer");

$assignment = new Assignment();

$stats = $assignment->getOfficerStatistics($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Maintenance Officer Dashboard | UMMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="../../assets/css/style.css">

</head>

<body class="bg-light">

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

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2>

                Welcome,
                <?= htmlspecialchars($_SESSION['firstname']); ?> 👋

            </h2>

            <p class="text-muted">

                Manage your assigned maintenance jobs.

            </p>

            <hr>

            <div class="row">

                <div class="col-md-3 mb-3">

                    <div class="card border-primary shadow-sm">

                        <div class="card-body text-center">

                            <h6>Total Jobs</h6>

                            <h2><?= $stats['total_jobs']; ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-warning shadow-sm">

                        <div class="card-body text-center">

                            <h6>Assigned</h6>

                            <h2><?= $stats['assigned']; ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-primary shadow-sm">

                        <div class="card-body text-center">

                            <h6>In Progress</h6>

                            <h2><?= $stats['in_progress']; ?></h2>

                        </div>

                    </div>

                </div>

                <div class="col-md-3 mb-3">

                    <div class="card border-success shadow-sm">

                        <div class="card-body text-center">

                            <h6>Completed</h6>

                            <h2><?= $stats['completed']; ?></h2>

                        </div>

                    </div>

                </div>

            </div>

            <hr>

            <h4 class="mb-4">

                Quick Actions

            </h4>

            <div class="row">

                <div class="col-md-4 mb-3">

                    <a href="my_jobs.php"
                       class="btn btn-primary w-100 py-3">

                        📋<br>

                        My Assigned Jobs

                    </a>

                </div>

                <div class="col-md-4 mb-3">

                    <a href="in_progress.php"
                       class="btn btn-warning w-100 py-3">

                        🔧<br>

                        In Progress

                    </a>

                </div>

                <div class="col-md-4 mb-3">

                    <a href="completed_jobs.php"
                       class="btn btn-success w-100 py-3">

                        ✅<br>

                        Completed Jobs

                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>

</html>