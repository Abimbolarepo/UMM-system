<?php

session_start();

require_once __DIR__ . "/../models/ServiceRequest.php";

$request = new ServiceRequest();

$action = $_GET['action'] ?? '';

switch ($action) {

    /*
    |--------------------------------------------------------------------------
    | CREATE MAINTENANCE REQUEST
    |--------------------------------------------------------------------------
    */

    case 'create':

        // Check whether user is logged in
        if (!isset($_SESSION['user_id'])) {

            $_SESSION['error'] = "Please login first.";

            header("Location:../views/auth/login.php");
            exit();
        }

        // Validate required fields
        if (
            empty($_POST['category_id']) ||
            empty($_POST['title']) ||
            empty($_POST['description']) ||
            empty($_POST['location']) ||
            empty($_POST['priority'])
        ) {

            $_SESSION['error'] = "Please complete all required fields.";

            header("Location:../../public/student/submit_request.php");
            exit();
        }

        /*
        |--------------------------------------------------------------------------
        | Generate Ticket Number
        |--------------------------------------------------------------------------
        */

        $ticketNumber = $request->generateTicket();

        /*
        |--------------------------------------------------------------------------
        | Image Upload
        |--------------------------------------------------------------------------
        */

        $imageName = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

            $uploadDir = "../../assets/uploads/maintenance/";

            if (!is_dir($uploadDir)) {

                mkdir($uploadDir, 0777, true);
            }

            $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($extension, $allowed)) {

                $imageName = uniqid("REQ_") . "." . $extension;

                move_uploaded_file(

                    $_FILES['image']['tmp_name'],

                    $uploadDir . $imageName
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Prepare Data
        |--------------------------------------------------------------------------
        */

        $data = [

            'ticket_number' => $ticketNumber,

            'user_id' => $_SESSION['user_id'],

            'category_id' => $_POST['category_id'],

            'title' => trim($_POST['title']),

            'description' => trim($_POST['description']),

            'location' => trim($_POST['location']),

            'building' => trim($_POST['building'] ?? ''),

            'room_number' => trim($_POST['room_number'] ?? ''),

            'priority' => $_POST['priority'],

            'image' => $imageName

        ];

        /*
        |--------------------------------------------------------------------------
        | Save Request
        |--------------------------------------------------------------------------
        */

        if ($request->create($data)) {

            $_SESSION['success'] = "Maintenance request submitted successfully.";

            // Temporary redirect until my_requests.php is created
            header("Location:../../public/student/my_requests.php");

            exit();
        }

        $_SESSION['error'] = "Unable to submit maintenance request.";

        header("Location:../../public/student/submit_request.php");

        exit();

        break;

    /*
    |--------------------------------------------------------------------------
    | DEFAULT
    |--------------------------------------------------------------------------
    */

    default:

        echo "Service Request Module Loaded";

        break;
}