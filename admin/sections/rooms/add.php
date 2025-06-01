<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Fetch hostels for dropdown
$hostels = [];
$hostelResult = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}

// Fetch room types for dropdown
$roomTypes = [];
$typeResult = $conn->query("SELECT id, type_name FROM room_types ORDER BY type_name ASC");
if ($typeResult && $typeResult->num_rows > 0) {
    $roomTypes = $typeResult->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="container mt-5">
    <a href="<?= BASE_URL ?>/admin/sections/rooms/index.php" class="btn btn-secondary mb-3">← Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Add New Room</h4>
        </div>
        <div class="card-body">
            <div id="formMessage"></div>

            <form id="addRoomForm">
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
                    <label for="floor_id" class="form-label">Floor</label>
                    <select name="floor_id" id="floor_id" class="form-select" required>
                        <option value="">-- Select Floor --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="room_number" class="form-label">Room Number</label>
                    <input type="text" name="room_number" id="room_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="max_capacity" class="form-label">Max Capacity</label>
                    <input type="number" name="max_capacity" id="max_capacity" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="room_type_id" class="form-label">Room Type</label>
                    <select name="room_type_id" id="room_type_id" class="form-select" required>
                        <option value="">-- Select Type --</option>
                        <?php foreach ($roomTypes as $type): ?>
                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">Add Room</button>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#hostel_id').on('change', function () {
        const hostelId = $(this).val();
        $('#floor_id').html('<option value="">Loading...</option>');

        $.ajax({
            url: '<?= BASE_URL ?>/admin/php_files/sections/floors/get_floors_by_hostel_id.php',
            type: 'GET',
            dataType: 'json',
            data: { hostel_id: hostelId },
            success: function (response) {
                if (response.success) {
                    let options = '<option value="">-- Select Floor --</option>';
                    response.data.forEach(function (floor) {
                        options += `<option value="${floor.id}">Floor ${floor.floor_number}</option>`;
                    });
                    $('#floor_id').html(options);
                } else {
                    $('#floor_id').html(`<option value="">${response.message}</option>`);
                }
            },
            error: function () {
                $('#floor_id').html('<option value="">Error loading floors</option>');
            }
        });
    });

    // Submit form via AJAX
    $('#addRoomForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '<?= BASE_URL . '/admin/php_files/sections/rooms/add.php' ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#formMessage').html(`<div class="alert alert-success">${response.message}</div>`);
                    $('#addRoomForm')[0].reset();
                    $('#floor_id').html('<option value="">-- Select Floor --</option>');
                } else {
                    $('#formMessage').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function(xhr) {
                $('#formMessage').html(`<div class="alert alert-danger">An error occurred.</div>`);
                console.log(xhr.responseText);
            }
        });
    });
});
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
