<?php

session_start();

require_once __DIR__ . "/../middleware/AuthMiddleware.php";
require_once __DIR__ . "/../middleware/RoleMiddleware.php";

require_once __DIR__ . "/../models/Assignment.php";
require_once __DIR__ . "/../models/ServiceRequest.php";

/*
|--------------------------------------------------------------------------
| Authentication & Authorization
|--------------------------------------------------------------------------
*/

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

/*
|--------------------------------------------------------------------------
| Only Accept POST Requests
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $_SESSION["error"] = "Invalid request.";

    header("Location: ../../views/admin/manage_requests.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Validate Request ID
|--------------------------------------------------------------------------
*/

$requestId = filter_input(INPUT_POST, "request_id", FILTER_VALIDATE_INT);

if (!$requestId) {

    $_SESSION["error"] = "Invalid maintenance request.";

    header("Location: ../../views/admin/manage_requests.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Validate Officer ID
|--------------------------------------------------------------------------
*/

$officerId = filter_input(INPUT_POST, "officer_id", FILTER_VALIDATE_INT);

if (!$officerId) {

    $_SESSION["error"] = "Please select a maintenance officer.";

    header("Location: ../../views/admin/view_request.php?id=" . $requestId);
    exit;
}

/*
|--------------------------------------------------------------------------
| Administrator ID
|--------------------------------------------------------------------------
*/

$adminId = $_SESSION["user_id"];

/*
|--------------------------------------------------------------------------
| Load Models
|--------------------------------------------------------------------------
*/

$assignment = new Assignment();

$serviceRequest = new ServiceRequest();

/*
|--------------------------------------------------------------------------
| Verify Request Exists
|--------------------------------------------------------------------------
*/

$request = $serviceRequest->getRequestDetails($requestId);

if (!$request) {

    $_SESSION["error"] = "Maintenance request not found.";

    header("Location: ../../views/admin/manage_requests.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Prevent Duplicate Assignment
|--------------------------------------------------------------------------
*/

if ($assignment->alreadyAssigned($requestId)) {

    $_SESSION["error"] = "This request has already been assigned.";

    header("Location: ../../views/admin/view_request.php?id=" . $requestId);
    exit;
}

/*
|--------------------------------------------------------------------------
| Assign Maintenance Officer
|--------------------------------------------------------------------------
*/

if ($assignment->assignOfficer($requestId, $officerId, $adminId)) {

    $_SESSION["success"] = "Maintenance request assigned successfully.";

} else {

    $_SESSION["error"] = "Unable to assign maintenance officer. Please try again.";
}

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

header("Location: ../../views/admin/view_request.php?id=" . $requestId);
exit;