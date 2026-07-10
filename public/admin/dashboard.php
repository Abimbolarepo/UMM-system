<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/Admin.php";

AuthMiddleware::check();

RoleMiddleware::authorize("Administrator");

/*
|--------------------------------------------------------------------------
| Load Dashboard Statistics
|--------------------------------------------------------------------------
*/

$admin = new Admin();

$stats = $admin->getDashboardStatistics();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Administrator Dashboard | UMMS</title>

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

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2 class="mb-3">

                Administrator Dashboard

            </h2>

            <p class="text-muted">

                Manage maintenance requests, users, categories and monitor system activities.

            </p>

            <hr>

            <!-- Dashboard Statistics -->

            <div class="row">

                <!-- Users -->

                <div class="col-md-2 mb-3">

                    <div class="card border-primary shadow-sm">

                        <div class="card-body text-center">

                            <h6>Total Users</h6>

                            <h2><?= $stats['total_users']; ?></h2>

                        </div>

                    </div>

                </div>

                <!-- Requests -->

                <div class="col-md-2 mb-3">

                    <div class="card border-success shadow-sm">

                        <div class="card-body text-center">

                            <h6>Total Requests</h6>

                            <h2><?= $stats['total_requests']; ?></h2>

                        </div>

                    </div>

                </div>

                <!-- Pending -->

                <div class="col-md-2 mb-3">

                    <div class="card border-warning shadow-sm">

                        <div class="card-body text-center">

                            <h6>Pending</h6>

                            <h2><?= $stats['pending']; ?></h2>

                        </div>

                    </div>

                </div>

                <!-- Assigned -->

                <div class="col-md-2 mb-3">

                    <div class="card border-info shadow-sm">

                        <div class="card-body text-center">

                            <h6>Assigned</h6>

                            <h2><?= $stats['assigned']; ?></h2>

                        </div>

                    </div>

                </div>

                <!-- In Progress -->

                <div class="col-md-2 mb-3">

                    <div class="card border-primary shadow-sm">

                        <div class="card-body text-center">

                            <h6>In Progress</h6>

                            <h2><?= $stats['in_progress']; ?></h2>

                        </div>

                    </div>

                </div>

                <!-- Completed -->

                <div class="col-md-2 mb-3">

                    <div class="card border-success shadow-sm">

                        <div class="card-body text-center">

                            <h6>Completed</h6>

                            <h2><?= $stats['completed']; ?></h2>

                        </div>

                    </div>

                </div>

            </div>

            <hr>

            <!-- Quick Actions -->

            <h3 class="mb-4">

                Quick Actions

            </h3>

            <div class="row">

                <!-- Manage Requests -->

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-primary shadow-sm h-100">

                        <div class="card-body text-center">

                            <div style="font-size:45px;">📋</div>

                            <h5 class="mt-3">

                                Manage Requests

                            </h5>

                            <p class="text-muted small">

                                View, search, assign and monitor all maintenance requests submitted by students and staff.

                            </p>

                            <a href="manage_requests.php"
                               class="btn btn-primary w-100">

                                Open Module

                            </a>

                        </div>

                    </div>

                </div>

                <!-- Manage Users -->

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-success shadow-sm h-100">

                        <div class="card-body text-center">

                            <div style="font-size:45px;">👥</div>

                            <h5 class="mt-3">

                                Manage Users

                            </h5>

                            <p class="text-muted small">

                                Create, edit, activate, deactivate and manage all system users.

                            </p>

                            <a href="manage_users.php"
                               class="btn btn-success w-100">

                                Open Module

                            </a>

                        </div>

                    </div>

                </div>

                <!-- Manage Categories -->

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-warning shadow-sm h-100">

                        <div class="card-body text-center">

                            <div style="font-size:45px;">🗂️</div>

                            <h5 class="mt-3">

                                Categories

                            </h5>

                            <p class="text-muted small">

                                Manage maintenance categories such as Electrical, Plumbing, ICT, Cleaning and more.

                            </p>

                            <a href="manage_categories.php"
                               class="btn btn-warning text-dark w-100">

                                Open Module

                            </a>

                        </div>

                    </div>

                </div>

                <!-- Reports -->

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="card border-dark shadow-sm h-100">

                        <div class="card-body text-center">

                            <div style="font-size:45px;">📊</div>

                            <h5 class="mt-3">

                                Reports

                            </h5>

                            <p class="text-muted small">

                                Generate reports, analyze maintenance activities and monitor system performance.

                            </p>

                            <a href="reports.php"
                               class="btn btn-dark w-100">

                                Open Module

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