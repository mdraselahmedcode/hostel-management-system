<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

$feeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($feeId <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch hostels
$hostels = [];
$hostelStmt = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($hostelStmt && $hostelStmt->num_rows > 0) {
    $hostels = $hostelStmt->fetch_all(MYSQLI_ASSOC);
}

// Fetch room types
$roomTypes = [];
$roomTypeStmt = $conn->query("SELECT id, type_name FROM room_types ORDER BY type_name ASC");
if ($roomTypeStmt && $roomTypeStmt->num_rows > 0) {
    $roomTypes = $roomTypeStmt->fetch_all(MYSQLI_ASSOC);
}

$billingCycles = ['monthly', 'quarterly', 'yearly'];

// Fetch fee record
$stmt = $conn->prepare("SELECT * FROM room_fees WHERE id = ?");
$stmt->bind_param("i", $feeId);
$stmt->execute();
$feeData = $stmt->get_result()->fetch_assoc();

if (!$feeData) {
    echo "<div class='alert alert-danger'>Invalid Room Fee ID.</div>";
    exit;
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container mt-5">
    <a href="<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>" class="btn btn-secondary mb-3">← Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Edit Room Fee</h4>
        </div>
        <div class="card-body">
            <form id="editFeesForm">
                <input type="hidden" name="id" value="<?= $feeData['id'] ?>">

                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="form-select" required>
                        <option value="">-- Select Hostel --</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>" <?= $hostel['id'] == $feeData['hostel_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hostel['hostel_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="room_type_id" class="form-label">Room Type</label>
                    <select name="room_type_id" id="room_type_id" class="form-select" required>
                        <option value="">-- Select Room Type --</option>
                        <?php foreach ($roomTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= $type['id'] == $feeData['room_type_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['type_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($feeData['price']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="billing_cycle" class="form-label">Billing Cycle</label>
                    <select name="billing_cycle" id="billing_cycle" class="form-select" required>
                        <option value="">-- Select Billing Cycle --</option>
                        <?php foreach ($billingCycles as $cycle): ?>
                            <option value="<?= $cycle ?>" <?= $cycle === $feeData['billing_cycle'] ? 'selected' : '' ?>>
                                <?= ucfirst($cycle) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="effective_from" class="form-label">Effective From</label>
                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= htmlspecialchars($feeData['effective_from']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Fee</button>
            </form>
            <div id="formMessage" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
    $('#editFeesForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: '<?= BASE_URL ?>/admin/php_files/sections/roomFees/update.php',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#formMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    $('#formMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#formMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
