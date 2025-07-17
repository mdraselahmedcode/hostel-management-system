<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Fetch hostels
$hostels = [];
$hostelStmt = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($hostelStmt && $hostelStmt->num_rows > 0) {
    $hostels = $hostelStmt->fetch_all(MYSQLI_ASSOC);
}

// Fetch room types (if needed globally)
$roomTypes = [];
$roomTypeStmt = $conn->query("SELECT id, type_name FROM room_types ORDER BY type_name ASC");
if ($roomTypeStmt && $roomTypeStmt->num_rows > 0) {
    $roomTypes = $roomTypeStmt->fetch_all(MYSQLI_ASSOC);
}

$billingCycles = ['monthly', 'quarterly', 'yearly'];

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container mt-5">
    <a href="<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>" class="btn btn-secondary mb-3 mt-4">← Back</a>

    <div id="formMessage" class="mt-3"></div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Add New Room Fees</h4>
        </div>
        <div class="card-body">
            <form id="addFeesForm">
                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="form-select" required>
                        <option value="">-- Select Hostel --</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>"><?= htmlspecialchars($hostel['hostel_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="room_type_id" class="form-label">Room Type</label>
                    <select name="room_type_id" id="room_type_id" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <!-- Options will be loaded via AJAX -->
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" name="price" id="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="billing_cycle" class="form-label">Billing Cycle</label>
                    <select name="billing_cycle" id="billing_cycle" class="form-select" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($billingCycles as $cycle): ?>
                            <option value="<?= $cycle ?>"><?= ucfirst($cycle) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="effective_from" class="form-label">Effective From</label>
                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Add Fee</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#hostel_id').on('change', function() {
            const hostelId = $(this).val();
            $('#room_type_id').html('<option value="">Loading...</option>');

            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/roomTypes/get_room_types_by_hostel_id.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    hostel_id: hostelId
                },
                success: function(response) {
                    let options = '<option value="">-- Select Type --</option>';
                    if (response.success && Array.isArray(response.data)) {
                        response.data.forEach(type => {
                            options += `<option value="${type.id}">${type.type_name}</option>`;
                        });
                    } else {
                        options = `<option value="">${response.message || 'No types found'}</option>`;
                    }
                    $('#room_type_id').html(options);
                },
                error: function() {
                    $('#room_type_id').html('<option value="">Error loading room types</option>');
                }
            });
        });

        // Submit form via AJAX
        $('#addFeesForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const formData = form.serialize();

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/roomFees/add.php' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        form[0].reset();
                        $('#room_type_id').html('<option value="">-- Select Type --</option>');
                    } else {
                        showSlideMessage(response.message || 'Failed to add fee.', 'danger');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    showSlideMessage('An error occurred. Please try again.', 'danger');
                }
            });
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>