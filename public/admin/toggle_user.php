<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../app/models/User.php";

$user = new User();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = $_GET['status'] ?? '';

if ($id <= 0 || $status == '') {
    $_SESSION['message'] = "Invalid request.";
    header("Location: manage_users.php");
    exit;
}

if ($user->updateStatus($id, $status)) {
    $_SESSION['message'] = "User status updated successfully.";
} else {
    $_SESSION['message'] = "Unable to update user status.";
}

header("Location: manage_users.php");
exit;