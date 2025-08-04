<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();
// Fetch hostels for dropdown
$hostels = [];
$hostelResult = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}

// Fetch room types for dropdown
$roomTypes = [];
$typeResult = $conn->query("
    SELECT id, type_name, default_capacity, buffer_limit 
    FROM room_types 
    ORDER BY type_name ASC
");
if ($typeResult && $typeResult->num_rows > 0) {
    $roomTypes = $typeResult->fetch_all(MYSQLI_ASSOC);
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
    </style>
</head>

<div class="content container mt-5">
    <a href="<?= BASE_URL . '/admin/sections/rooms/index.php' ?>" class="btn btn-secondary mt-4 mb-3">← Back to Rooms</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Add New Room</h4>
        </div>
        <div class="card-body">

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
                        <!-- Will be populated dynamically -->
                    </select>
                </div>

                <div class="mb-3">
                    <label for="room_number" class="form-label">Room Number</label>
                    <input type="text" name="room_number" id="room_number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="room_type_id" class="form-label">Room Type</label>
                    <select name="room_type_id" id="room_type_id" class="form-select" required>
                        <!-- Will be populated dynamically -->
                    </select>
                </div>

                <div class="mb-3">
                    <label for="max_capacity" class="form-label">Max Capacity</label>
                    <input type="number" name="max_capacity" id="max_capacity" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success">Add Room</button>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#hostel_id').on('change', function() {
            const hostelId = $(this).val();

            // Fetch Floors
            $('#floor_id').html('<option value="">Loading...</option>');
            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/floors/get_floors_by_hostel_id.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    hostel_id: hostelId
                },
                success: function(response) {
                    let options = '<option value="">-- Select Floor --</option>';
                    if (response.success) {
                        response.data.forEach(function(floor) {
                            options += `<option value="${floor.id}">Floor ${floor.floor_number}</option>`;
                        });
                    } else {
                        options = `<option value="">${response.message}</option>`;
                    }
                    $('#floor_id').html(options);
                },
                error: function() {
                    $('#floor_id').html('<option value="">Error loading floors</option>');
                }
            });

            // Fetch Room Types
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
                    if (response.success) {
                        response.data.forEach(function(type) {
                            options += `<option value="${type.id}">${type.type_name}</option>`;
                        });
                    } else {
                        options = `<option value="">${response.message}</option>`;
                    }
                    $('#room_type_id').html(options);
                },
                error: function() {
                    $('#room_type_id').html('<option value="">Error loading room types</option>');
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
                        showSlideMessage(response.message, 'success');
                        $('#addRoomForm')[0].reset();
                        $('#floor_id').html('<option value="">-- Select Floor --</option>');
                    } else {
                        showSlideMessage(response.message || 'Failed to add room.', 'danger');
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