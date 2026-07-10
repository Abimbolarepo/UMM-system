<!-- Navigation Bar -->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

    <div class="container-fluid">

        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="#">

            <i class="fas fa-tools me-2"></i>

            UMMS

        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarContent">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse"
             id="navbarContent">

            <ul class="navbar-nav ms-auto align-items-center">

                <!-- Notifications -->

                <li class="nav-item dropdown me-3">

                    <a class="nav-link position-relative"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown">

                        <i class="fas fa-bell"></i>

                        <span
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                            3

                        </span>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a class="dropdown-item" href="#">
                                New Request Assigned
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#">
                                Request Completed
                            </a>
                        </li>

                    </ul>

                </li>

                <!-- User -->

                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown">

                        <i class="fas fa-user-circle me-1"></i>

                        <?php
                        echo isset($_SESSION['user'])
                            ? $_SESSION['user']['firstname']
                            : 'Guest';
                        ?>

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a class="dropdown-item"
                               href="#">
                                Profile
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item text-danger"
                               href="<?= BASE_URL ?>/logout">

                                Logout

                            </a>
                        </li>

                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav>