<?php
/**
 * Application Configuration
 */

define('APP_NAME', 'University Maintenance Management System');
define('APP_URL', 'http://localhost/maintenance-system');
define('APP_VERSION', '1.0.0');

define('DB_HOST', 'localhost');
define('DB_NAME', 'maintenance_db');
define('DB_USER', 'root');
define('DB_PASS', '');

date_default_timezone_set('Africa/Lagos');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}