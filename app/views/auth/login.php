<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Miva Open University | UMMS Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <style>

        body{

            min-height:100vh;

            background:linear-gradient(135deg,#12385A,#1F4F7A);

            display:flex;

            align-items:center;

            justify-content:center;

            font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;

        }

        .login-card{

            border:none;

            border-radius:20px;

            overflow:hidden;

            box-shadow:0 20px 40px rgba(0,0,0,.25);

        }

        .logo{

            width:150px;

            height:auto;
        }

        .system-title{

            color:#12385A;

            font-weight:700;
        }

        .btn-login{

            background:#12385A;

            border:none;
        }

        .btn-login:hover{

            background:#0e2d49;
        }

        .feature{

            color:#6c757d;

            font-size:15px;
        }

        .feature i{

            color:#12385A;

            margin-right:8px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="row justify-content-center">

        <div class="col-lg-5 col-md-7">

            <div class="card login-card">

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <img
                            src="../../../assets/images/miva-logo.png"
                            class="logo mb-3"
                            alt="Miva Open University">

                        <h3 class="system-title">

                            University Maintenance Management System

                        </h3>

                        <p class="text-muted mb-0">

                            Miva Open University

                        </p>

                    </div>

                    <?php if(isset($_SESSION['error'])): ?>

                        <div class="alert alert-danger">

                            <?= $_SESSION['error']; ?>

                        </div>

                        <?php unset($_SESSION['error']); ?>

                    <?php endif; ?>

                    <?php if(isset($_SESSION['success'])): ?>

                        <div class="alert alert-success">

                            <?= $_SESSION['success']; ?>

                        </div>

                        <?php unset($_SESSION['success']); ?>

                    <?php endif; ?>

                    <form
                        method="POST"
                        action="../../controllers/AuthController.php?action=login">

                        <div class="mb-3">

                            <label class="form-label">

                                Email Address

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control form-control-lg"
                                placeholder="Enter your email"
                                required>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">

                                Password

                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control form-control-lg"
                                placeholder="Enter your password"
                                required>

                        </div>

                        <button
                            class="btn btn-login btn-lg text-white w-100">

                            <i class="bi bi-box-arrow-in-right"></i>

                            Login

                        </button>

                    </form>

                    <div class="text-center mt-4">

                        Don't have an account?

                        <a href="register.php">

                            Register Here

                        </a>

                    </div>

                    <hr class="my-4">

                    <div class="row text-center">

                        <div class="col-12 feature mb-2">

                            <i class="bi bi-tools"></i>

                            Report Maintenance Issues

                        </div>

                        <div class="col-12 feature mb-2">

                            <i class="bi bi-clipboard-check"></i>

                            Track Requests

                        </div>

                        <div class="col-12 feature">

                            <i class="bi bi-person-workspace"></i>

                            Maintenance Workflow

                        </div>

                    </div>

                </div>

            </div>

            <div class="text-center text-white mt-4">

                © <?= date('Y'); ?>

                Miva Open University

                <br>

                University Maintenance Management System

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>