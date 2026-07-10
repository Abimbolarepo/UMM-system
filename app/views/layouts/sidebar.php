<?php

$userRole = $_SESSION['user']['role_name'] ?? '';

?>

<div class="bg-dark text-white vh-100 p-3 sidebar">

    <h5 class="text-center mb-4">

        <i class="fas fa-university"></i>

        UMMS

    </h5>

    <ul class="nav flex-column">

        <!-- Dashboard -->

        <li class="nav-item mb-2">

            <a class="nav-link text-white"
               href="#">

                <i class="fas fa-home me-2"></i>

                Dashboard

            </a>

        </li>

        <?php if($userRole == 'Student' || $userRole == 'Staff'): ?>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-plus-circle me-2"></i>

                    Submit Request

                </a>

            </li>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-list me-2"></i>

                    My Requests

                </a>

            </li>

        <?php endif; ?>

        <?php if($userRole == 'Maintenance Officer'): ?>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-clipboard-list me-2"></i>

                    Assigned Jobs

                </a>

            </li>

        <?php endif; ?>

        <?php if($userRole == 'Administrator'): ?>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-users me-2"></i>

                    Users

                </a>

            </li>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-folder-open me-2"></i>

                    Categories

                </a>

            </li>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-file-alt me-2"></i>

                    Requests

                </a>

            </li>

            <li class="nav-item mb-2">

                <a class="nav-link text-white"
                   href="#">

                    <i class="fas fa-chart-bar me-2"></i>

                    Reports

                </a>

            </li>

        <?php endif; ?>

    </ul>

</div>