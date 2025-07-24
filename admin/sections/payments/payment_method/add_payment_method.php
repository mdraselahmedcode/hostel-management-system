<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_admin();

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
            INSERT INTO payment_methods 
            (name, display_name, account_number, active, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("sssi", $name, $display_name, $account_number, $active);
        $stmt->execute();

        $_SESSION['message'] = "Payment method added successfully";
        header("Location: payment_methods.php");
        exit();
    }
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-credit-card"></i> Add Payment Method</h2>
                <a href="payment_methods.php" class="btn btn-outline-secondary">
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
                    <form id="addPaymentMethodForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">System Name*</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            <small class="text-muted">Internal identifier (e.g. 'bkash')</small>
                        </div>

                        <div class="mb-3">
                            <label for="display_name" class="form-label">Display Name*</label>
                            <input type="text" class="form-control" id="display_name" name="display_name" required
                                value="<?= htmlspecialchars($_POST['display_name'] ?? '') ?>">
                            <small class="text-muted">Name shown to users (e.g. 'bKash Personal')</small>
                        </div>

                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                value="<?= htmlspecialchars($_POST['account_number'] ?? '') ?>">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="active" name="active" checked>
                            <label class="form-check-label" for="active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Payment Method
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>


<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>
    $(document).ready(function () {
        $('#addPaymentMethodForm').on('submit', function (e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);

            showSlideMessage('Adding new method...', 'info');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/payment_method/add_payment_method.php' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        form.reset();
                    } else {
                        showSlideMessage(response.message || 'An error occurred.', 'danger');
                    }
                },
                error: function (xhr, status, error) {
                    showSlideMessage('Server error: ' + error, 'danger');
                }
            });
        });
    });
</script>



<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>