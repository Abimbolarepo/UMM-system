<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$requestModel = new ServiceRequest();

$requestId = $_GET['id'] ?? null;

if (!$requestId) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: manage_requests.php");
    exit;
}

$request = $requestModel->getRequestDetails($requestId);

if (!$request) {
    $_SESSION['error'] = "Request not found.";
    header("Location: manage_requests.php");
    exit;
}

$officers = $requestModel->getMaintenanceOfficers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $officerId = $_POST['officer_id'] ?? '';

    if (empty($officerId)) {

        $_SESSION['error'] = "Please select a Maintenance Officer.";

    } else {

        if ($requestModel->assignOfficer($requestId, $officerId)) {

            $_SESSION['success'] = "Maintenance Officer assigned successfully.";

            header("Location: manage_requests.php");
            exit;

        } else {

            $_SESSION['error'] = "Unable to assign officer.";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Assign Maintenance Officer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>Assign Maintenance Officer</h4>

</div>

<div class="card-body">

<?php if(isset($_SESSION['error'])): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($_SESSION['error']); ?>

</div>

<?php unset($_SESSION['error']); endif; ?>

<div class="mb-3">

<label class="form-label"><strong>Ticket Number</strong></label>

<input class="form-control"
value="<?= htmlspecialchars($request['ticket_number']); ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label"><strong>Title</strong></label>

<input class="form-control"
value="<?= htmlspecialchars($request['title']); ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label"><strong>Requester</strong></label>

<input class="form-control"
value="<?= htmlspecialchars($request['fullname']); ?>"
readonly>

</div>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Select Maintenance Officer

</label>

<select
name="officer_id"
class="form-select"
required>

<option value="">Choose Officer</option>

<?php foreach($officers as $officer): ?>

<option value="<?= $officer['user_id']; ?>">

<?= htmlspecialchars($officer['fullname']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<button class="btn btn-success">

Assign Officer

</button>

<a href="manage_requests.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

</body>
</html>