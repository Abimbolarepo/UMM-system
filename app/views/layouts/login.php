<?php

$title = "Login";
$pageCSS = "login.css";

require_once "../app/views/layouts/header.php";
?>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-header bg-primary text-white text-center">

                    <h3>
                        <i class="fas fa-tools"></i>
                        UMMS Login
                    </h3>

                </div>

                <div class="card-body">

                    <?php if(isset($_SESSION['error'])): ?>

                        <div class="alert alert-danger">

                            <?= $_SESSION['error']; ?>

                        </div>

                    <?php endif; ?>

                    <form method="POST"
                          action="<?= BASE_URL ?>/authenticate">

                        <div class="mb-3">

                            <label class="form-label">

                                Email Address

                            </label>

                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Password

                            </label>

                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>

                        </div>

                        <div class="d-grid">

                            <button type="submit"
                                    class="btn btn-primary">

                                Login

                            </button>

                        </div>

                    </form>

                    <hr>

                    <div class="text-center">

                        Don't have an account?

                        <a href="<?= BASE_URL ?>/register">

                            Register Here

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

require_once "../app/views/layouts/footer.php";

?>