<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../app/controllers/CategoryController.php";

$controller = new CategoryController();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = $controller->destroy($id);

$_SESSION['message'] = $result['message'];

header("Location: manage_categories.php");

exit;