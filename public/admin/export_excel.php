<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/ServiceRequest.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

$requestModel = new ServiceRequest();

$requests = $requestModel->getAllRequests();

$filename = "UMMS_Report_" . date("Y-m-d_H-i-s") . ".csv";

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen("php://output", "w");

/*
|--------------------------------------------------------------------------
| Column Headings
|--------------------------------------------------------------------------
*/

fputcsv($output, [

    "Ticket Number",
    "Requester",
    "Category",
    "Title",
    "Location",
    "Building",
    "Room",
    "Priority",
    "Status",
    "Date Created"

]);

/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

foreach ($requests as $request) {

    fputcsv($output, [

        $request['ticket_number'],
        $request['fullname'],
        $request['category_name'],
        $request['title'],
        $request['location'],
        $request['building'],
        $request['room_number'],
        $request['priority'],
        $request['status'],
        date("d M Y", strtotime($request['created_at']))

    ]);

}

fclose($output);
exit;