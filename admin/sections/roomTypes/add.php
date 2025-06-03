<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';
require_once BASE_PATH . '/admin/includes/header_admin.php';


// Fetch hostels for dropdown
$hostels = [];
$hostelResult = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name");
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}



?>

<div class="content container mt-5">
    <a href="<?= BASE_URL ?>/admin/sections/roomTypes/index.php" class="btn btn-secondary mb-3">← Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Add New Room Type</h4>
        </div>
        <div class="card-body">
            <form id="addRoomTypeForm">
                <!-- Filtering by hostel by selection -->
                <div class="mb-3 ">
                    <label for="hostel_id" class="form-label">Select Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="form-select">
                        <option value="">-- Select Hostel --</option>
                        <?php foreach($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>"><?= htmlspecialchars($hostel['hostel_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type_name" class="form-label">Room Type Name</label>
                    <input type="text" name="type_name" id="type_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="default_capacity" class="form-label">Default Capacity</label>
                    <input type="number" name="default_capacity" id="default_capacity" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="buffer_limit" class="form-label">Buffer Limit</label>
                    <input type="number" name="buffer_limit" id="buffer_limit" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Add Room Type</button>
            </form>
            <div id="formMessage" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#addRoomTypeForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '<?= BASE_URL ?>/admin/php_files/sections/roomTypes/add.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                const messageDiv = $('#formMessage');
                if (response.success) {
                    messageDiv.html(`<div class="alert alert-success">${response.message}</div>`);
                    $('#addRoomTypeForm')[0].reset();

                    // Fade out success message after 3 seconds
                    setTimeout(() => {
                        messageDiv.find('.alert-success').fadeOut('slow', function () {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    messageDiv.html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function (xhr) {
                $('#formMessage').html(`<div class="alert alert-danger">An error occurred.</div>`);
                console.error(xhr.responseText);
            }
        });
    });
});
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
