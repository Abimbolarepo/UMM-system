<?php
session_start();

// Load application configuration
require_once 'app/config/App.php';

// For now, redirect to the login page
header("Location: app/views/auth/login.php");
exit;