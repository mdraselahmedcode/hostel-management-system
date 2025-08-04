<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_admin();

// Get all payment methods
$methods = $conn->query("SELECT * FROM payment_methods ORDER BY name")->fetch_all(MYSQLI_ASSOC);

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<head>
    <style>
        :root {
            --primary-color: #394e63ff;
            --primary-hover: #1c2935ff;
            --primary-text: #ffffff;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.5) !important;
        }

        a.text-primary:hover,
        a.text-primary:focus {
            color: var(--primary-hover) !important;
            text-decoration: underline;
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent !important;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            color: var(--primary-text) !important;
            background-color: var(--primary-color) !important;
            border-color: var(--primary-hover) !important;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25) !important;
        }

        .card.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }
    </style>
</head>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/sections/payments/index.php' ?>" class="btn btn-secondary mb-3 mt-4">Back</a>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-credit-card"></i> Payment Methods</h2>
                <a href="add_payment_method.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add New Method
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Display Name</th>
                                    <th>Account Number</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($methods)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No payment methods found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($methods as $method): ?>
                                        <tr>
                                            <td><?= $method['id'] ?></td>
                                            <td><?= htmlspecialchars($method['name']) ?></td>
                                            <td><?= htmlspecialchars($method['display_name']) ?></td>
                                            <td><?= htmlspecialchars($method['account_number'] ?? 'N/A') ?></td>
                                            <td>
                                                <button class="btn btn-sm toggle-status <?= $method['active'] ? 'btn-success' : 'btn-secondary' ?>"
                                                    data-id="<?= $method['id'] ?>"
                                                    data-status="<?= $method['active'] ?>">
                                                    <?= $method['active'] ? 'Active' : 'Inactive' ?>
                                                </button>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="edit_payment_method.php?id=<?= $method['id'] ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <button class="btn btn-sm btn-danger delete-method" data-id="<?= $method['id'] ?>">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Delete method
        $('.delete-method').click(function() {
            const id = $(this).data('id');
            const row = $(this).closest('tr');

            if (!confirm('Are you sure you want to delete this payment method?')) return;

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/payment_method/delete_method.php' ?>',
                type: 'POST',
                data: {
                    id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('An error occurred while deleting.', 'danger');
                }
            });
        });

        // Toggle status
        $('.toggle-status').click(function() {
            const button = $(this);
            const id = button.data('id');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/payment_method/toggle_status.php' ?>',
                type: 'POST',
                data: {
                    id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        // Update button appearance
                        if (response.active) {
                            button.removeClass('btn-secondary').addClass('btn-success').text('Active');
                        } else {
                            button.removeClass('btn-success').addClass('btn-secondary').text('Inactive');
                        }
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('An error occurred while updating status.', 'danger');
                }
            });
        });
    });
</script>



<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>