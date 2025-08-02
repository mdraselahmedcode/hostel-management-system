<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/student/includes/header_student.php';

require_student();

$student_id = $_SESSION['student']['id'];

// For re-populating form after error
$old = [
    'hostel_id' => '',
    'floor_id' => '',
    'room_id' => '',
    'reason' => '',
    'details' => ''
];

$errors = [];
$success_message = '';

$reasons = ['roommate issues', 'too noisy', 'prefer different location', 'other'];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $hostel_id = isset($_POST['hostel_id']) ? (int)$_POST['hostel_id'] : 0;
    $floor_id = isset($_POST['floor_id']) ? (int)$_POST['floor_id'] : 0;
    $room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : null;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $details = isset($_POST['details']) ? trim($_POST['details']) : '';

    $old = compact('hostel_id', 'floor_id', 'room_id', 'reason', 'details');

    // Validate required fields
    if (!$hostel_id) {
        $errors[] = "Please select a hostel.";
    }
    if (!$floor_id) {
        $errors[] = "Please select a floor.";
    }
    if (!$room_id) {
        $errors[] = "Please select a preferred room.";
    }
    if (!$reason || !in_array($reason, $reasons)) {
        $errors[] = "Please select a valid reason.";
    }

    // Optional: Validate that the selected room belongs to the floor and hostel
    if (!$errors) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM rooms WHERE id = ? AND floor_id = ? AND hostel_id = ?");
        $stmt->bind_param("iii", $room_id, $floor_id, $hostel_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            $errors[] = "Selected room does not belong to the selected floor and hostel.";
        }
    }

    if (!$errors) {
        // Insert room change request
        $stmt = $conn->prepare("INSERT INTO room_change_requests (student_id, preferred_room_id, reason, details) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $student_id, $room_id, $reason, $details);

        if ($stmt->execute()) {
            $success_message = "Room change request submitted successfully.";
            // Clear old inputs
            $old = ['hostel_id' => '', 'floor_id' => '', 'room_id' => '', 'reason' => '', 'details' => ''];
        } else {
            $errors[] = "Failed to submit request. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch all hostels for dropdown
$hostels = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<div class="content container-fluid">
    <div class="row full-height">
        <?php include BASE_PATH . '/student/includes/sidebar_student.php'; ?>

        <main class="col-12 col-md-10 col-lg-10 py-5 pt-3" style="max-height: calc(100vh - 142.75px); overflow-y:auto;">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-4">New Room Change Request</h2>
                <div class="mb-4">
                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <form method="post" id="roomChangeForm" novalidate>
                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel <span class="text-danger">*</span></label>
                    <select id="hostel_id" name="hostel_id" class="form-select" required>
                        <option value="">-- Select Hostel --</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>" <?= ($old['hostel_id'] == $hostel['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hostel['hostel_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="floor_id" class="form-label">Floor <span class="text-danger">*</span></label>
                    <select id="floor_id" name="floor_id" class="form-select" required disabled>
                        <option value="">-- Select Floor --</option>
                        <!-- Filled dynamically -->
                    </select>
                </div>

                <div class="mb-3">
                    <label for="room_id" class="form-label">Preferred Room <span class="text-danger">*</span></label>
                    <select id="room_id" name="room_id" class="form-select" required disabled>
                        <option value="">-- Select Room --</option>
                        <!-- Filled dynamically -->
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <select id="reason" name="reason" class="form-select" required>
                        <option value="">-- Select Reason --</option>
                        <?php foreach ($reasons as $r): ?>
                            <option value="<?= $r ?>" <?= ($old['reason'] === $r) ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">Additional Details</label>
                    <textarea id="details" style="resize: none;" name="details" rows="4" class="form-control"><?= htmlspecialchars($old['details']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        function resetDropdown($dropdown, placeholder) {
            $dropdown.html(`<option value="">${placeholder}</option>`);
            $dropdown.prop('disabled', true);
        }

        // Load floors on hostel change
        $('#hostel_id').change(function() {
            var hostelId = $(this).val();
            resetDropdown($('#floor_id'), '-- Select Floor --');
            resetDropdown($('#room_id'), '-- Select Room --');

            if (hostelId) {
                $.getJSON('get_floors.php', {
                    hostel_id: hostelId
                }, function(data) {
                    if (data.length > 0) {
                        $('#floor_id').prop('disabled', false);
                        $.each(data, function(i, floor) {
                            $('#floor_id').append(`<option value="${floor.id}">${floor.floor_number} ${floor.floor_name ? '- ' + floor.floor_name : ''}</option>`);
                        });
                        <?php if ($old['floor_id']): ?>
                            $('#floor_id').val(<?= (int)$old['floor_id'] ?>).trigger('change');
                        <?php endif; ?>
                    }
                });
            }
        });

        // Load rooms on floor change
        $('#floor_id').change(function() {
            var floorId = $(this).val();
            resetDropdown($('#room_id'), '-- Select Room --');

            if (floorId) {
                $.getJSON('get_rooms.php', {
                    floor_id: floorId
                }, function(data) {
                    if (data.length > 0) {
                        $('#room_id').prop('disabled', false);
                        $.each(data, function(i, room) {
                            $('#room_id').append(`<option value="${room.id}">${room.room_number}</option>`);
                        });
                        <?php if ($old['room_id']): ?>
                            $('#room_id').val(<?= (int)$old['room_id'] ?>);
                        <?php endif; ?>
                    }
                });
            }
        });

        // Trigger initial load if old values exist
        <?php if ($old['hostel_id']): ?>
            $('#hostel_id').trigger('change');
        <?php endif; ?>

        // AJAX form submission
        $('#roomChangeForm').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var formData = $form.serialize();

            $.ajax({
                url: '<?= BASE_URL . '/student/php_files/sections/room_change_request/create.php' ?>', // current page or backend endpoint URL for processing
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    // Optionally disable button or show loading indicator
                    $form.find('button[type="submit"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        // Reset form & dropdowns
                        $form[0].reset();
                        resetDropdown($('#floor_id'), '-- Select Floor --');
                        resetDropdown($('#room_id'), '-- Select Room --');
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('An error occurred. Please try again.', 'danger');
                },
                complete: function() {
                    $form.find('button[type="submit"]').prop('disabled', false);
                }
            });
        });
    });
</script>


<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>