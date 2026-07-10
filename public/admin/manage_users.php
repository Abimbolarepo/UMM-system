<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/User.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$userModel = new User();

$search = trim($_GET['search'] ?? "");
$role = trim($_GET['role'] ?? "");

$users = $userModel->getAllUsers();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Manage Users | UMMS</title>

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

    <div class="alert alert-success">

        <?= htmlspecialchars($_SESSION['success']); ?>

    </div>

</div>

<?php unset($_SESSION['success']); ?>

<?php endif; ?>

<?php if (isset($_SESSION['error'])) : ?>

<div class="container mt-3">

    <div class="alert alert-danger">

        <?= htmlspecialchars($_SESSION['error']); ?>

    </div>

</div>

<?php unset($_SESSION['error']); ?>

<?php endif; ?>

<div class="container mt-4">

<div class="card shadow">

<div class="card-header bg-success text-white">

<div class="d-flex justify-content-between align-items-center">

<h3 class="mb-0">

Manage System Users

</h3>

<a href="create_user.php"
   class="btn btn-light">

+ Create User

</a>

</div>

</div>

<div class="card-body">

<form method="GET">

<div class="row mb-4">

<div class="col-md-6">

<input
type="text"
name="search"
class="form-control"
placeholder="Search name or email"
value="<?= htmlspecialchars($search); ?>">

</div>

<div class="col-md-3">

<select
name="role"
class="form-select">

<option value="">All Roles</option>

<option value="Administrator"
<?= $role=="Administrator" ? "selected" : ""; ?>>

Administrator

</option>

<option value="Maintenance Officer"
<?= $role=="Maintenance Officer" ? "selected" : ""; ?>>

Maintenance Officer

</option>

<option value="Student"
<?= $role=="Student" ? "selected" : ""; ?>>

Student

</option>

</select>

</div>

<div class="col-md-3 d-grid">

<button
class="btn btn-primary">

Search

</button>

</div>

</div>

</form>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>Name</th>

<th>Email</th>

<th>Phone</th>

<th>Role</th>

<th>Status</th>

<th>Date Created</th>

<th width="250">

Actions

</th>

</tr>

</thead>

<tbody>

<?php if (!empty($users)) : ?>

<?php foreach ($users as $user) : ?>

<tr>

<td>

<?= htmlspecialchars($user['firstname']." ".$user['lastname']); ?>

</td>

<td>

<?= htmlspecialchars($user['email']); ?>

</td>

<td>

<?= htmlspecialchars($user['phone']); ?>

</td>

<td>

<?php

switch ($user['role_name']) {

    case "Administrator":
        $badge = "danger";
        break;

    case "Maintenance Officer":
        $badge = "primary";
        break;

    default:
        $badge = "secondary";
}

?>

<span class="badge bg-<?= htmlspecialchars($badge); ?>">

<?= htmlspecialchars($user['role_name']); ?>

</span>

</td>

<td>

<?php if($user['status']==="Active"): ?>

<span class="badge bg-success">

Active

</span>

<?php else: ?>

<span class="badge bg-dark">

Inactive

</span>

<?php endif; ?>

</td>

<td>

<?= date("d M Y",strtotime($user['created_at'])); ?>

</td>

<td>

<a
href="view_user.php?id=<?= (int)$user['user_id']; ?>"
class="btn btn-primary btn-sm">

View

</a>

<a
href="edit_user.php?id=<?= (int)$user['user_id']; ?>"
class="btn btn-warning btn-sm">

Edit

</a>

<?php if($user['status']==="Active"): ?>

<a
href="toggle_user.php?id=<?= (int)$user['user_id']; ?>&status=Inactive"
class="btn btn-danger btn-sm">

Deactivate

</a>

<?php else: ?>

<a
href="toggle_user.php?id=<?= (int)$user['user_id']; ?>&status=Active"
class="btn btn-success btn-sm">

Activate

</a>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="7"
class="text-center text-muted">

No users found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

<hr>

<div class="d-flex justify-content-between">

<a
href="dashboard.php"
class="btn btn-secondary">

← Back to Dashboard

</a>

<h5>

Total Users:

<strong>

<?= count($users); ?>

</strong>

</h5>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>