<?php

session_start();

require_once __DIR__ . '/../models/User.php';

$user = new User();

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'register':
        register($user);
        break;

    case 'login':
        login($user);
        break;

    case 'logout':
        logout();
        break;

    default:
        header("Location: ../views/auth/login.php");
        exit();
}


/*
|--------------------------------------------------------------------------
| REGISTER USER
|--------------------------------------------------------------------------
*/

function register($user)
{
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {

        header("Location: ../views/auth/register.php");
        exit();
    }

    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $department = trim($_POST['department']);
    $role = trim($_POST['role']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validation
    if (
        empty($firstname) ||
        empty($lastname) ||
        empty($email) ||
        empty($password)
    ) {

        $_SESSION['error'] = "Please fill in all required fields.";

        header("Location: ../views/auth/register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $_SESSION['error'] = "Invalid email address.";

        header("Location: ../views/auth/register.php");
        exit();
    }

    if ($password != $confirm) {

        $_SESSION['error'] = "Passwords do not match.";

        header("Location: ../views/auth/register.php");
        exit();
    }

    if (strlen($password) < 6) {

        $_SESSION['error'] = "Password must be at least 6 characters.";

        header("Location: ../views/auth/register.php");
        exit();
    }

    if ($user->emailExists($email)) {

        $_SESSION['error'] = "Email already exists.";

        header("Location: ../views/auth/register.php");
        exit();
    }

    /*
    |------------------------------------------------------------
    | Convert Role Name to Role ID
    |------------------------------------------------------------
    | Ensure these IDs match your roles table.
    */

    $roles = [

        "Administrator" => 1,
        "Maintenance Officer" => 2,
        "Student" => 3,
        "Staff" => 4

    ];

    $role_id = $roles[$role] ?? 3;

    $data = [

        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'phone' => $phone,
        'department' => $department,
        'password' => $password,
        'role_id' => $role_id

    ];

    if ($user->create($data)) {

        $_SESSION['success'] = "Registration Successful. Please Login.";

        header("Location: ../views/auth/login.php");
        exit();
    }

    $_SESSION['error'] = "Registration Failed.";

    header("Location: ../views/auth/register.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| LOGIN USER
|--------------------------------------------------------------------------
*/

function login($user)
{
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {

        header("Location: ../views/auth/login.php");
        exit();
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $_SESSION['error'] = "Email and Password are required.";

        header("Location: ../views/auth/login.php");
        exit();
    }

    $loggedUser = $user->findByEmail($email);

    if (!$loggedUser) {

        $_SESSION['error'] = "Invalid Login Credentials.";

        header("Location: ../views/auth/login.php");
        exit();
    }

    if (!password_verify($password, $loggedUser['password'])) {

        $_SESSION['error'] = "Incorrect Password.";

        header("Location: ../views/auth/login.php");
        exit();
    }

    if ($loggedUser['status'] != "Active") {

        $_SESSION['error'] = "Your account has been deactivated.";

        header("Location: ../views/auth/login.php");
        exit();
    }

    /*
    |--------------------------------------------------------------------------
    | Create Session
    |--------------------------------------------------------------------------
    */

    session_regenerate_id(true);

    $_SESSION['user_id'] = $loggedUser['user_id'];
    $_SESSION['firstname'] = $loggedUser['firstname'];
    $_SESSION['lastname'] = $loggedUser['lastname'];
    $_SESSION['email'] = $loggedUser['email'];
    $_SESSION['role_id'] = $loggedUser['role_id'];
    $_SESSION['role_name'] = $loggedUser['role_name'];

    /*
    |--------------------------------------------------------------------------
    | Redirect by Role
    |--------------------------------------------------------------------------
    */

    switch ($loggedUser['role_name']) {

        case "Administrator":

            header("Location: ../../public/admin/dashboard.php");
            exit();

        case "Maintenance Officer":

            header("Location: ../../public/officer/dashboard.php");
            exit();

        case "Student":

            header("Location: ../../public/student/dashboard.php");
            exit();

        case "Staff":

            header("Location: ../../public/student/dashboard.php");
            exit();

        default:

            session_destroy();

            $_SESSION['error'] = "Invalid User Role.";

            header("Location: ../views/auth/login.php");
            exit();
    }
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

function logout()
{
    session_start();

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: ../views/auth/login.php");
    exit();
}