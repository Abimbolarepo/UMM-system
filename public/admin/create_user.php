<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../app/models/User.php";
require_once "../../app/core/Database.php";

$user = new User();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $department= trim($_POST['department']);
    $roleId    = $_POST['role_id'];
    $password  = $_POST['password'];

    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($email) ||
        empty($password)
    ) {

        $message = "Please fill all required fields.";

    } elseif ($user->emailExists($email)) {

        $message = "Email already exists.";

    } else {

        if (
            $user->createUser(
                $firstname,
                $lastname,
                $email,
                $phone,
                $department,
                $roleId,
                $password
            )
        ) {

            $_SESSION['message'] = "User created successfully.";

            header("Location: manage_users.php");
            exit;

        } else {

            $message = "Unable to create user.";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Create User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h3>Create User</h3>

</div>

<div class="card-body">

<?php if($message): ?>

<div class="alert alert-danger">

<?= $message ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>First Name</label>

<input
type="text"
name="firstname"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Last Name</label>

<input
type="text"
name="lastname"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Phone</label>

<input
type="text"
name="phone"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Department</label>

<input
type="text"
name="department"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Role</label>

<select
name="role_id"
class="form-select">

<option value="1">Administrator</option>

<option value="2">Maintenance Officer</option>

<option value="3">Student</option>

</select>

</div>

<div class="col-md-6 mb-4">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

</div>

<div class="d-flex justify-content-end">

<a
href="manage_users.php"
class="btn btn-secondary me-2">

Cancel

</a>

<button
class="btn btn-primary">

<i class="bi bi-person-plus-fill"></i>

Create User

</button>

</div>

</form>

</div>

</div>

</div>

</body>

</html>