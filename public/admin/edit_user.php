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

if ($userId === (int) $_SESSION['user_id']) {

    $_SESSION['error'] = "You cannot edit your own account here.";

    header("Location: manage_users.php");

    exit;
}

/*
|--------------------------------------------------------------------------
| Handle Update
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname  = trim($_POST['firstname']);
    $lastname   = trim($_POST['lastname']);
    $email      = trim($_POST['email']);
    $phone      = trim($_POST['phone']);
    $department = trim($_POST['department']);
    $roleId     = (int) $_POST['role'];
    $status     = trim($_POST['status']);

    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($email)
    ) {

        $_SESSION['error'] = "Please complete all required fields.";

    } elseif (!in_array($roleId, [1, 2, 3], true)) {

        $_SESSION['error'] = "Invalid role selected.";

    } elseif (!in_array($status, ['Active', 'Inactive'], true)) {

        $_SESSION['error'] = "Invalid account status.";

    } elseif ($userModel->emailExistsForAnotherUser($email, $userId)) {

        $_SESSION['error'] = "Email address already exists.";

    } else {

        if ($userModel->updateUser(

            $userId,
            $firstname,
            $lastname,
            $email,
            $phone,
            $department,
            $roleId,
            $status

        )) {

            $_SESSION['success'] = "User updated successfully.";

            header("Location: manage_users.php");

            exit;

        } else {

            $_SESSION['error'] = "Unable to update user.";

        }

    }

}

?>
<?php
$currentRole   = $_POST['role']   ?? $user['role_id'];
$currentStatus = $_POST['status'] ?? $user['status'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Edit User | UMMS</title>

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

        <div class="card-header bg-warning">

            <h3 class="mb-0">Edit User</h3>

        </div>

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">First Name <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="firstname"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['firstname'] ?? $user['firstname']); ?>"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">Last Name <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="lastname"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['lastname'] ?? $user['lastname']); ?>"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['email'] ?? $user['email']); ?>"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">Phone</label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['phone'] ?? $user['phone']); ?>">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">Department</label>

                        <input
                            type="text"
                            name="department"
                            class="form-control"
                            value="<?= htmlspecialchars($_POST['department'] ?? $user['department']); ?>">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">Role</label>

                        <select
                            name="role"
                            class="form-select"
                            required>

                            <option value="1"
                                <?= $currentRole == 1 ? "selected" : ""; ?>>
                                Administrator
                            </option>

                            <option value="2"
                                <?= $currentRole == 2 ? "selected" : ""; ?>>
                                Maintenance Officer
                            </option>

                            <option value="3"
                                <?= $currentRole == 3 ? "selected" : ""; ?>>
                                Student
                            </option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">Status</label>

                        <select
                            name="status"
                            class="form-select"
                            required>

                            <option value="Active"
                                <?= $currentStatus == "Active" ? "selected" : ""; ?>>
                                Active
                            </option>

                            <option value="Inactive"
                                <?= $currentStatus == "Inactive" ? "selected" : ""; ?>>
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>

                <div class="d-flex justify-content-between mt-4">

                    <a href="manage_users.php"
                       class="btn btn-secondary">

                        ← Cancel

                    </a>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>