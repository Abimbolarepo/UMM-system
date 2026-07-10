<?php
// =============================================
// University Maintenance Management System
// Header Layout
// =============================================

// Change this if your project folder name changes
define('BASE_URL', 'http://localhost/maintenance-system');
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= isset($title) ? htmlspecialchars($title) : "University Maintenance Management System"; ?>
    </title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/style.css">

    <?php
    // Optional page-specific stylesheet
    if (isset($pageCSS)) {
        echo '<link rel="stylesheet" href="' . BASE_URL . '/assets/css/' . htmlspecialchars($pageCSS) . '">';
    }
    ?>

</head>

<body>

<div class="wrapper">