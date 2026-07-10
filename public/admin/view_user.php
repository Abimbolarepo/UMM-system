<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/User.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid user selected.";
    header("Location: manage_users.php");
    exit;
}

$userId = (int) $_GET['id'];

$userModel = new User();

$user = $userModel->getUserById($userId);

if (!$user) {
    $_SESSION['error'] = "User not found.";
    header("Location: manage_users.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>View User | UMMS</title>

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

<?php if (isset($_SESSION['success'])) : ?>

    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>

<?php if (isset($_SESSION['error'])) : ?>

    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3 class="mb-0">User Details</h3>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="200">First Name</th>
                    <td><?= htmlspecialchars($user['firstname'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Last Name</th>
                    <td><?= htmlspecialchars($user['lastname'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Phone</th>
                    <td><?= htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Department</th>
                    <td><?= htmlspecialchars($user['department'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Role</th>
                    <td><?= htmlspecialchars($user['role'] ?? 'N/A'); ?></td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>

                        <?php if (($user['status'] ?? '') === 'Active'): ?>

                            <span class="badge bg-success">Active</span>

                        <?php else: ?>

                            <span class="badge bg-danger">Inactive</span>

                        <?php endif; ?>

                    </td>
                </tr>

                <tr>
                    <th>Date Registered</th>
                    <td>
                        <?= !empty($user['created_at'])
                            ? date("d M Y h:i A", strtotime($user['created_at']))
                            : 'N/A'; ?>
                    </td>
                </tr>

            </table>

            <div class="d-flex justify-content-between mt-4">

                <a href="manage_users.php"
                   class="btn btn-secondary">

                    ← Back to Manage Users

                </a>

                <a href="edit_user.php?id=<?= (int) $user['user_id']; ?>"
                   class="btn btn-warning">

                    Edit User

                </a>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>