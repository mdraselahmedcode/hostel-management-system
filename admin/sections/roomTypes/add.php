<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php'; 

require_admin(); 

require_once BASE_PATH . '/admin/includes/header_admin.php';

// Fetch hostels for dropdown
$hostels = [];
$hostelResult = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name");
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}
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
    </style>
</head>

<div class="container mt-5 px-3 mt-5" >
    <a href="<?= BASE_URL ?>/admin/sections/roomTypes/index.php" class="btn btn-outline-secondary mb-4 mt-5">
        ← Back to Room Types
    </a>

    <div class="card shadow-lg rounded-3 mt-2">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="bi bi-plus-circle me-2 fs-4"></i>
            <h5 class="mb-0">Add New Room Type</h5>
        </div>
        <div class="card-body p-4">
            <form id="addRoomTypeForm" class="needs-validation" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="hostel_id" class="form-label">Select Hostel <span class="text-danger">*</span></label>
                        <select name="hostel_id" id="hostel_id" class="form-select" required>
                            <option value="">-- Select Hostel --</option>
                            <?php foreach ($hostels as $hostel): ?>
                                <option value="<?= $hostel['id'] ?>"><?= htmlspecialchars($hostel['hostel_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Choose the hostel where this room type belongs.</small>
                        <div class="invalid-feedback">Please select a hostel.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="type_name" class="form-label">Room Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="type_name" id="type_name" class="form-control" required>
                        <small class="form-text text-muted">Enter a descriptive name like "Single", "Double", "Suite", etc.</small>
                        <div class="invalid-feedback">Room type name is required.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="default_capacity" class="form-label">Default Capacity <span class="text-danger">*</span></label>
                        <input type="number" name="default_capacity" id="default_capacity" class="form-control" min="1" required>
                        <small class="form-text text-muted">Set how many people this room type can accommodate by default.</small>
                        <div class="invalid-feedback">Enter a valid capacity.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="buffer_limit" class="form-label">Buffer Limit <span class="text-danger">*</span></label>
                        <input type="number" name="buffer_limit" id="buffer_limit" class="form-control" min="0">
                        <small class="form-text text-muted">Specify extra capacity allowed beyond the default (if any).</small>
                        <div class="invalid-feedback">Enter a valid buffer limit.</div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center mb-5">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save2 me-1"></i> Save Room Type
                    </button>
                    <!-- Removed #formMessage div -->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script for form handling -->
<script>
    (() => {
        'use strict';
        const form = document.querySelector('#addRoomTypeForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            form.classList.add('was-validated');

            if (!form.checkValidity()) return;

            const formData = $(this).serialize();

            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/roomTypes/add.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        form.reset();
                        form.classList.remove('was-validated');
                    } else {
                        showSlideMessage(response.message || 'Failed to add room type.', 'danger');
                    }
                },
                error: function(xhr) {
                    showSlideMessage('An unexpected error occurred.', 'danger');
                    console.error(xhr.responseText);
                }
            });
        });
    })();
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
