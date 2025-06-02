<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';
require_once BASE_PATH . '/admin/includes/header_admin.php';

// Get room type ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: index.php?success=Invalid room type ID.");
    exit;
}

// Fetch existing room type
$stmt = $conn->prepare("SELECT * FROM room_types WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$roomType = $result->fetch_assoc();

if (!$roomType) {
    header("Location: index.php?success=Room type not found.");
    exit;
}
?>

<div class="content container mt-5">
    <a href="<?= BASE_URL ?>/admin/sections/roomTypes/index.php" class="btn btn-secondary mb-3">← Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Edit Room Type</h4>
        </div>
        <div class="card-body">
            <form id="editRoomTypeForm">
                <input type="hidden" name="id" value="<?= htmlspecialchars($roomType['id']) ?>">

                <div class="mb-3">
                    <label for="type_name" class="form-label">Room Type Name</label>
                    <input type="text" name="type_name" id="type_name" class="form-control" required value="<?= htmlspecialchars($roomType['type_name']) ?>">
                </div>

                <div class="mb-3">
                    <label for="default_capacity" class="form-label">Default Capacity</label>
                    <input type="number" name="default_capacity" id="default_capacity" class="form-control" required value="<?= (int)$roomType['default_capacity'] ?>">
                </div>

                <div class="mb-3">
                    <label for="buffer_limit" class="form-label">Buffer Limit</label>
                    <input type="number" name="buffer_limit" id="buffer_limit" class="form-control" required value="<?= (int)$roomType['buffer_limit'] ?>">
                </div>

                <button type="submit" class="btn btn-primary">Update Room Type</button>
            </form>
            <div id="formMessage" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#editRoomTypeForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        console.log("formData", formData); 
        $.ajax({
            url: '<?= BASE_URL ?>/admin/php_files/sections/roomTypes/edit.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                const messageDiv = $('#formMessage');
                if (response.success) {
                    messageDiv.html(`<div class="alert alert-success">${response.message}</div>`);
                    setTimeout(() => {
                        messageDiv.find('.alert-success').fadeOut('slow', function () {
                            $(this).remove();
                            // window.location.href = "index.php?success=Room type updated successfully.";
                        });
                    }, 2000);
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
