<?php

session_start();

$message = $_SESSION['message'] ?? '';

unset($_SESSION['message']);

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once "../../app/controllers/CategoryController.php";

$controller = new CategoryController();

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $categories = $controller->search($search);
} else {
    $categories = $controller->index();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Manage Categories</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">
                Maintenance Categories
            </h2>

            <p class="text-muted">
                Manage all maintenance request categories.
            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-house-door-fill"></i>
                Dashboard
            </a>

            <a href="add_category.php" class="btn btn-warning">
                <i class="bi bi-plus-circle"></i>
                Add Category
            </a>

        </div>

    </div>

    <?php if ($message): ?>

    <div class="alert alert-info">

        <?= htmlspecialchars($message) ?>

    </div>

    <?php endif; ?>

    <form method="GET" class="row mb-4">

        <div class="col-md-10">

            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search category..."
                value="<?= htmlspecialchars($search) ?>">

        </div>

        <div class="col-md-2 d-grid">

            <button class="btn btn-dark">

                <i class="bi bi-search"></i>

                Search

            </button>

        </div>

    </form>

    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-hover align-middle">

                <thead class="table-dark">

                <tr>

                    <th>#</th>

                    <th>Category</th>

                    <th>Description</th>

                    <th>Total Requests</th>

                    <th width="180">Actions</th>

                </tr>

                </thead>

                <tbody>

                <?php if (empty($categories)): ?>

                    <tr>

                        <td colspan="5">

                            <div class="text-center py-5">

                                <i class="bi bi-folder2-open display-3 text-secondary"></i>

                                <h5 class="mt-3">No Categories Found</h5>

                                <p class="text-muted">
                                    Try another search or add a new category.
                                </p>

                            </div>

                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($categories as $index => $category): ?>

                        <tr>

                            <td><?= $index + 1 ?></td>

                            <td>

                                <strong>

                                    <?= htmlspecialchars($category['category_name']) ?>

                                </strong>

                            </td>

                            <td>

                                <?= !empty($category['description'])
                                    ? htmlspecialchars($category['description'])
                                    : '<span class="text-muted">No description</span>' ?>

                            </td>

                            <td>

                                <?php
                                $count = (int) $category['total_requests'];
                                $badge =
                                    $count === 0 ? 'secondary' :
                                    ($count <= 2  ? 'primary'   : 'danger');
                                ?>

                                <span class="badge bg-<?= $badge ?>">
                                    <?= $count ?>
                                </span>

                            </td>

                            <td>

                                <a
                                    href="edit_category.php?id=<?= (int) $category['category_id'] ?>"
                                    class="btn btn-sm btn-success">

                                    <i class="bi bi-pencil-square"></i>

                                </a>

                                <a
                                    href="delete_category.php?id=<?= (int) $category['category_id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">

                                    <i class="bi bi-trash"></i>

                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>