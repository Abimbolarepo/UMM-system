<?php

session_start();

require_once "../../app/middleware/AuthMiddleware.php";
require_once "../../app/middleware/RoleMiddleware.php";
require_once "../../app/models/User.php";

AuthMiddleware::check();
RoleMiddleware::authorize("Administrator");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid user selected.";

    header("Location: manage_users.php");
    exit;
}

$userId = (int) $_GET['id'];

if ($userId === (int) $_SESSION['user_id']) {

    $_SESSION['error'] = "You cannot delete your own account.";

    header("Location: manage_users.php");
    exit;
}

$userModel = new User();

$user = $userModel->getUserById($userId);

if (!$user) {

    $_SESSION['error'] = "User not found.";

    header("Location: manage_users.php");
    exit;
}

if ($userModel->deleteUser($userId)) {

    $_SESSION['success'] = "User deleted successfully.";

} else {

    $_SESSION['error'] = "Unable to delete user.";

}

header("Location: manage_users.php");
exit;