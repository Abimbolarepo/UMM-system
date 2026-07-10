<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/config/database.php";
require_once "../../app/models/Assignment.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Maintenance Officer");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid request selected.";
    header("Location: my_jobs.php");
    exit;
}

$requestId = (int) $_GET['id'];
$officerId = $_SESSION['user_id'];

$assignmentModel = new Assignment();

/*
|--------------------------------------------------------------------------
| Verify the Job Belongs to the Logged-in Officer
|--------------------------------------------------------------------------
*/

$job = $assignmentModel->getOfficerJob($requestId, $officerId);

if (!$job) {
    $_SESSION['error'] = "You are not authorized to access this job.";
    header("Location: my_jobs.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Only Assigned Jobs Can Be Started
|--------------------------------------------------------------------------
*/

if ($job['request_status'] !== "Assigned") {

    $_SESSION['error'] = "This job has already been started or completed.";

    header("Location: view_job.php?id=" . $requestId);

    exit;
}

$db = Database::getInstance()->getConnection();

try {

    $db->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Update Service Request
    |--------------------------------------------------------------------------
    */

    $updateRequest = $db->prepare("
        UPDATE service_requests
        SET
            status = 'In Progress',
            updated_at = NOW()
        WHERE request_id = ?
    ");

    if (!$updateRequest->execute([$requestId])) {
        throw new Exception("Failed to update service request.");
    }

    /*
    |--------------------------------------------------------------------------
    | Update Assignment Status
    |--------------------------------------------------------------------------
    */

    $updateAssignment = $db->prepare("
        UPDATE assignments
        SET
            status = 'In Progress'
        WHERE request_id = ?
            AND officer_id = ?
    ");

    if (!$updateAssignment->execute([
        $requestId,
        $officerId
    ])) {
        throw new Exception("Failed to update assignment.");
    }

    $db->commit();

    $_SESSION['success'] = "Job has been started successfully.";

} catch (Exception $e) {

    if ($db->inTransaction()) {
        $db->rollBack();
    }

    $_SESSION['error'] = $e->getMessage();
}

header("Location: view_job.php?id=" . $requestId);

exit;