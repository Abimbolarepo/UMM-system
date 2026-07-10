<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../app/controllers/CategoryController.php";

$controller = new CategoryController();

$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = $controller->store($_POST);

    $message = $result['message'];
    $success = $result['success'];

    if ($success) {
        header("Location: manage_categories.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Add Category</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Add Category</h3>

</div>

<div class="card-body">

<?php if($message): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($message) ?>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Category Name

</label>

<input
type="text"
name="category_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"></textarea>

</div>

<button class="btn btn-warning">

Save Category

</button>

<a href="manage_categories.php" class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</body>

</html>