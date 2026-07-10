    </div>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="<?= BASE_URL ?>/assets/js/app.js"></script>

    <?php
    // Optional page-specific JavaScript
    if (isset($pageJS)) {
        echo '<script src="' . BASE_URL . '/assets/js/' . htmlspecialchars($pageJS) . '"></script>';
    }
    ?>

</body>
</html>