<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account - UMMS</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../../../assets/css/style.css">

    <link rel="stylesheet" href="../../../assets/css/login.css">

</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-7">

            <div class="card shadow">

                <div class="card-header bg-success text-white text-center">

                    <h2>University Maintenance Management System</h2>

                    <h5>Create Account</h5>

                </div>

                <div class="card-body">

                    <!-- Error Message -->

                    <?php if(isset($_SESSION['error'])): ?>

                        <div class="alert alert-danger">

                            <?= $_SESSION['error']; ?>

                        </div>

                        <?php unset($_SESSION['error']); ?>

                    <?php endif; ?>

                    <!-- Success Message -->

                    <?php if(isset($_SESSION['success'])): ?>

                        <div class="alert alert-success">

                            <?= $_SESSION['success']; ?>

                        </div>

                        <?php unset($_SESSION['success']); ?>

                    <?php endif; ?>

                    <form method="POST"
                          action="../../controllers/AuthController.php?action=register">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    First Name

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="firstname"
                                    placeholder="Enter First Name"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Last Name

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    name="lastname"
                                    placeholder="Enter Last Name"
                                    required>

                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Email Address

                            </label>

                            <input
                                type="email"
                                class="form-control"
                                name="email"
                                placeholder="example@university.edu"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Phone Number

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="phone"
                                placeholder="08012345678">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Department

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="department"
                                placeholder="Computer Science">

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Select Role

                            </label>

                            <select
                                class="form-select"
                                name="role"
                                required>

                                <option value="">-- Select Role --</option>

                                <option value="Student">Student</option>

                                <option value="Staff">Staff</option>

                                <option value="Maintenance Officer">
                                    Maintenance Officer
                                </option>

                            </select>

                        </div>

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Password

                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    name="password"
                                    required>

                            </div>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">

                                    Confirm Password

                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    name="confirm_password"
                                    required>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-success w-100">

                            Register

                        </button>

                    </form>

                    <hr>

                    <div class="text-center">

                        Already have an account?

                        <a href="login.php">

                            Login Here

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>