<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_admin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: payment_methods.php");
    exit();
}

$id = (int)$_GET['id'];

// Get current method data
$method = $conn->query("SELECT * FROM payment_methods WHERE id = $id")->fetch_assoc();
if (!$method) {
    $_SESSION['message'] = "Payment method not found";
    header("Location: payment_methods.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $display_name = trim($_POST['display_name']);
    $account_number = trim($_POST['account_number'] ?? null);
    $active = isset($_POST['active']) ? 1 : 0;

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($display_name)) {
        $errors[] = "Display name is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            UPDATE payment_methods 
            SET name = ?, 
                display_name = ?, 
                account_number = ?, 
                active = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("sssii", $name, $display_name, $account_number, $active, $id);
        $stmt->execute();

        $_SESSION['message'] = "Payment method updated successfully";
        header("Location: payment_methods.php");
        exit();
    }
}

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-credit-card"></i> Edit Payment Method</h2>
                <a href="<?= BASE_URL . '/admin/sections/payments/payment_method/index.php' ?>" class="btn btn-outline-secondary mb-3 mt-4">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="editPaymentMethodForm">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="mb-3">
                            <label for="name" class="form-label">System Name*</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="<?= htmlspecialchars($method['name']) ?>">
                            <small class="text-muted">Internal identifier (lowercase, no spaces)</small>
                        </div>

                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name*</label>
                            <input type="text" class="form-control" id="display_name" name="display_name" required
                                value="<?= htmlspecialchars($method['display_name']) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                value="<?= htmlspecialchars($method['account_number'] ?? '') ?>">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="active" name="active"
                                <?= $method['active'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Payment Method
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#editPaymentMethodForm').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);

            showSlideMessage('Updating...', 'info');

            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/payments/payment_method/edit_payment_method.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        setTimeout(() => {
                            window.location.href = '<?= BASE_URL ?>/admin/sections/payments/payment_method/index.php';
                        }, 1500);
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    showSlideMessage('Server error: ' + error, 'danger');
                }
            });
        });
    });
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>