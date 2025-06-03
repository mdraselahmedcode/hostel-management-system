<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage_fees.php'); // or appropriate page
    exit;
}

$id = (int) $_GET['id'];
$errors = [];
$successMessage = '';

// Fetch current fee record
$stmt = $conn->prepare("
    SELECT rf.*, rt.type_name, f.floor_number, h.id AS hostel_id
    FROM room_fees rf
    LEFT JOIN room_types rt ON rf.room_type_id = rt.id
    LEFT JOIN floors f ON rf.floor_id = f.id
    LEFT JOIN hostels h ON f.hostel_id = h.id
    WHERE rf.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$fee = $result->fetch_assoc();

if (!$fee) {
    echo "<div class='alert alert-danger'>Fee record not found.</div>";
    exit;
}

// Fetch all billing cycle options
$billingCycles = ['monthly', 'quarterly', 'yearly'];

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $billing_cycle = $_POST['billing_cycle'] ?? '';
    $effective_from = $_POST['effective_from'] ?? '';

    if (!is_numeric($price)) {
        $errors[] = "Price must be a valid number.";
    }
    if (!in_array($billing_cycle, $billingCycles)) {
        $errors[] = "Invalid billing cycle.";
    }
    if (!strtotime($effective_from)) {
        $errors[] = "Effective date is invalid.";
    }

    if (empty($errors)) {
        $updateStmt = $conn->prepare("
            UPDATE room_fees
            SET price = ?, billing_cycle = ?, effective_from = ?
            WHERE id = ?
        ");
        $updateStmt->bind_param('dssi', $price, $billing_cycle, $effective_from, $id);

        if ($updateStmt->execute()) {
            $successMessage = "Room fee updated successfully.";
            // Refresh fee data
            $fee['price'] = $price;
            $fee['billing_cycle'] = $billing_cycle;
            $fee['effective_from'] = $effective_from;
        } else {
            $errors[] = "Failed to update. Please try again.";
        }
    }
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container mt-5">
    <a href="<?= BASE_URL ?>/admin/sections/roomFees/index.php" class="btn btn-secondary mb-3">Back</a>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-4">Edit Room Fee</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Room Type</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($fee['type_name']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Floor</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($fee['floor_number']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hostel</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($fee['hostel_id']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="text" name="price" class="form-control" value="<?= htmlspecialchars($fee['price']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Billing Cycle</label>
                    <select name="billing_cycle" class="form-select" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($billingCycles as $cycle): ?>
                            <option value="<?= $cycle ?>" <?= $fee['billing_cycle'] === $cycle ? 'selected' : '' ?>>
                                <?= ucfirst($cycle) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Effective From</label>
                    <input type="date" name="effective_from" class="form-control"
                           value="<?= htmlspecialchars(date('Y-m-d', strtotime($fee['effective_from']))) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Fee</button>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
