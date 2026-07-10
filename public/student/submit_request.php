<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/config/database.php";

AuthMiddleware::check();

$db = Database::getInstance()->getConnection();

$categories = $db->query("
    SELECT *
    FROM categories
    ORDER BY category_name
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Submit Maintenance Request | UMMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../../assets/css/style.css">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>📝 Submit Maintenance Request</h3>

</div>

<div class="card-body">

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


<form method="POST"
action="../../app/controllers/ServiceRequestController.php?action=create"
enctype="multipart/form-data">

<!-- Category -->

<div class="mb-3">

<label class="form-label">Category</label>

<select
name="category_id"
class="form-select"
required>

<option value="">Select Category</option>

<?php foreach($categories as $category): ?>

<option value="<?= $category['category_id']; ?>">

<?= htmlspecialchars($category['category_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>


<!-- Title -->

<div class="mb-3">

<label class="form-label">Title</label>

<input
type="text"
name="title"
class="form-control"
placeholder="Brief title of the problem"
required>

</div>


<!-- Description -->

<div class="mb-3">

<label class="form-label">Description</label>

<textarea
name="description"
rows="5"
class="form-control"
placeholder="Describe the maintenance issue..."
required></textarea>

</div>


<!-- Location -->

<div class="mb-3">

<label class="form-label">Location</label>

<input
type="text"
name="location"
class="form-control"
placeholder="Example: Hostel B"
required>

</div>


<!-- Building -->

<div class="mb-3">

<label class="form-label">Building</label>

<select
name="building"
class="form-select">

<option value="">Select Building</option>

<option>Engineering Block</option>

<option>ICT Building</option>

<option>Science Block</option>

<option>Administrative Block</option>

<option>Library</option>

<option>Faculty of Arts</option>

<option>Faculty of Education</option>

<option>Faculty of Management Sciences</option>

<option>Hostel A</option>

<option>Hostel B</option>

<option>Hostel C</option>

</select>

</div>


<!-- Room Number -->

<div class="mb-3">

<label class="form-label">Room Number</label>

<input
type="text"
name="room_number"
class="form-control"
placeholder="Example: Room 204">

</div>


<!-- Priority -->

<div class="mb-3">

<label class="form-label">Priority</label>

<select
name="priority"
class="form-select"
required>

<option value="Low">Low</option>

<option value="Medium" selected>Medium</option>

<option value="High">High</option>

<option value="Critical">Critical</option>

</select>

</div>


<!-- Image Upload -->

<div class="mb-3">

<label class="form-label">Upload Image (Optional)</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*">

</div>


<div class="d-flex justify-content-between">

<a href="dashboard.php"

class="btn btn-secondary">

← Back

</a>

<button
type="submit"
class="btn btn-primary">

Submit Request

</button>

</div>

</form>

</div>

</div>

</div>

</body>

</html>