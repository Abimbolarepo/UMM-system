<?php if (!$assigned) : ?>

<div class="card shadow-sm">

    <div class="card-header bg-warning">

        <h5 class="mb-0">
            Assign Maintenance Officer
        </h5>

    </div>

    <div class="card-body">

        <form
            action="../../app/controllers/AssignmentController.php"
            method="POST">

            <input
                type="hidden"
                name="request_id"
                value="<?= $requestId; ?>">

            <div class="mb-3">

                <label
                    for="officer_id"
                    class="form-label">

                    Select Maintenance Officer

                </label>

                <select
                    name="officer_id"
                    id="officer_id"
                    class="form-select"
                    required>

                    <option value="">
                        -- Select Officer --
                    </option>

                    <?php foreach ($officers as $officer) : ?>

                        <option
                            value="<?= $officer['user_id']; ?>">

                            <?= htmlspecialchars($officer['fullname']); ?>
                            (<?= htmlspecialchars($officer['department']); ?>)

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Assign Officer

            </button>

        </form>

    </div>

</div>

<?php endif; ?>