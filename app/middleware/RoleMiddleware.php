<?php

class RoleMiddleware
{
    public static function authorize($roles)
    {
        if (!isset($_SESSION['role_name'])) {

            header("Location: ../../app/views/auth/login.php");
            exit();

        }

        // Convert a single role to an array
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!in_array($_SESSION['role_name'], $roles)) {

            http_response_code(403);

            die("<h2>403 - Access Denied</h2>");
        }
    }
}