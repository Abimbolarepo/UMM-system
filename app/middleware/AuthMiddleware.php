<?php

class AuthMiddleware
{
    public static function check()
    {
        if (!isset($_SESSION['user_id'])) {

            $_SESSION['error'] = "Please login first.";

            header("Location: ../../app/views/auth/login.php");

            exit();
        }
    }
}