<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';
require_once BASE_PATH . '/admin/includes/header_admin.php';

require_once BASE_PATH . '/admin/includes/csrf.php';
$csrfToken = generate_csrf_token();


// Get room type ID
$id = $_GET['roomTypeId'] ?? null;

// Get editable hostel id 
$filterHostelId = $_GET['hostel_id'] ?? null;

// Validate ID
if (!$id || !is_numeric($id)) {
    header("Location: index.php?success=Invalid room type ID.");
    exit;
}

// Validate hostel id
if (!$filterHostelId || !is_numeric($filterHostelId)) {
    header("Location: index.php?success=Invalid Hostel ID.");
    exit;
}

// Fetch existing room type
$stmt = $conn->prepare("SELECT * FROM room_types WHERE id = ? And hostel_id = ?");
$stmt->bind_param("ii", $id, $filterHostelId);
$stmt->execute();
$result = $stmt->get_result();
$roomType = $result->fetch_assoc();





if (!$roomType) {
    header("Location: index.php?success=Room type not found.");
    exit;
}
?>

<div class="container mt-5 px-3 mt-5">
    <a href="<?= BASE_URL ?>/admin/sections/roomTypes/index.php" class="btn btn-outline-secondary mb-4 mt-4">
        ← Back to Room Types
    </a>

    <div class="card shadow rounded-4 border-0">
        <div class="card-header bg-primary text-white d-flex align-items-center rounded-top-4">
            <i class="bi bi-pencil-square me-2 fs-4"></i>
            <h5 class="mb-0">Edit Room Type</h5>
        </div>
        <div class="card-body p-4">
            <form id="editRoomTypeForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($roomType['id']) ?>">  
                <input type="hidden" name="hostel_id" value="<?= htmlspecialchars($roomType['hostel_id']) ?>">  

                <div class="mb-3">
                    <label for="type_name" class="form-label">Room Type Name <span class="text-danger">*</span></label>
                    <input type="text" name="type_name" id="type_name" class="form-control" required value="<?= htmlspecialchars($roomType['type_name']) ?>">
                    <small class="text-muted">E.g., Single, Double, Suite, etc.</small>
                </div>

                <div class="mb-3">
                    <label for="default_capacity" class="form-label">Default Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="default_capacity" id="default_capacity" class="form-control" required min="1" value="<?= (int)$roomType['default_capacity'] ?>">
                    <small class="text-muted">Default number of people the room can hold.</small>
                </div>

                <div class="mb-3">
                    <label for="buffer_limit" class="form-label">Buffer Limit <span class="text-danger">*</span></label>
                    <input type="number" name="buffer_limit" id="buffer_limit" class="form-control" required min="0" value="<?= (int)$roomType['buffer_limit'] ?>">
                    <small class="text-muted">Extra capacity allowed (optional for flexible planning).</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save2 me-1"></i> Update Room Type
                    </button>
                    <div id="formMessage" class="ms-3 flex-grow-1"></div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
$(document).ready(function () {
    $('#editRoomTypeForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
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
