<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/response_helper.php';

// Check if student ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$studentId = intval($_GET['id']);

// Fetch student data
$studentQuery = $conn->prepare("
    SELECT 
        students.*,
        hostels.hostel_name,
        rooms.room_number,
        floors.floor_number,
        floors.floor_name,
        
        -- Permanent address with explicit aliases
        perm_addr.id AS perm_addr_id,
        perm_addr.country_id AS perm_country_id,
        perm_addr.state AS perm_state,
        perm_addr.division AS perm_division,
        perm_addr.district AS perm_district,
        perm_addr.sub_district AS perm_sub_district,
        perm_addr.village AS perm_village,
        perm_addr.postalcode AS perm_postalcode,
        perm_addr.street AS perm_street,
        perm_addr.house_no AS perm_house_no,
        perm_addr.detail AS perm_detail,
        
        -- Temporary address with explicit aliases
        temp_addr.id AS temp_addr_id,
        temp_addr.country_id AS temp_country_id,
        temp_addr.state AS temp_state,
        temp_addr.division AS temp_division,
        temp_addr.district AS temp_district,
        temp_addr.sub_district AS temp_sub_district,
        temp_addr.village AS temp_village,
        temp_addr.postalcode AS temp_postalcode,
        temp_addr.street AS temp_street,
        temp_addr.house_no AS temp_house_no,
        temp_addr.detail AS temp_detail,
        
        -- Country names
        perm_country.country_name AS perm_country_name,
        temp_country.country_name AS temp_country_name
        
    FROM students
    LEFT JOIN hostels ON students.hostel_id = hostels.id
    LEFT JOIN rooms ON students.room_id = rooms.id
    LEFT JOIN floors ON rooms.floor_id = floors.id
    LEFT JOIN addresses AS perm_addr ON students.permanent_address_id = perm_addr.id
    LEFT JOIN countries AS perm_country ON perm_addr.country_id = perm_country.id
    LEFT JOIN addresses AS temp_addr ON students.temporary_address_id = temp_addr.id
    LEFT JOIN countries AS temp_country ON temp_addr.country_id = temp_country.id
    WHERE students.id = ?
");
$studentQuery->bind_param('i', $studentId);
$studentQuery->execute();
$studentResult = $studentQuery->get_result();

if ($studentResult->num_rows === 0) {
    header("Location: index.php");
    exit;
}

$student = $studentResult->fetch_assoc();

// Fetch dropdown data
$countryQuery = $conn->query("SELECT id, country_name FROM countries");
$countries = $countryQuery->fetch_all(MYSQLI_ASSOC);

$hostelQuery = $conn->query("SELECT id, hostel_name FROM hostels");
$hostels = $hostelQuery->fetch_all(MYSQLI_ASSOC);

// Fetch floors for the student's current hostel
$floors = [];
if ($student['hostel_id']) {
    $floorQuery = $conn->prepare("SELECT id, floor_number, floor_name FROM floors WHERE hostel_id = ?");
    $floorQuery->bind_param('i', $student['hostel_id']);
    $floorQuery->execute();
    $floors = $floorQuery->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch rooms for the student's current floor
$rooms = [];
if ($student['floor_id']) {
    $roomQuery = $conn->prepare("SELECT id, room_number FROM rooms WHERE floor_id = ?");
    $roomQuery->bind_param('i', $student['floor_id']);
    $roomQuery->execute();
    $rooms = $roomQuery->get_result()->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Student - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="container mt-4">
    <h2>Edit Student</h2>
    <a href="<?= BASE_URL . '/admin/sections/students/index.php' ?>" class="btn btn-sm btn-secondary mb-3">Back</a>

    <form id="edit-student-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        <input type="hidden" name="permanent_address_id" value="<?= $student['permanent_address_id'] ?>">
        <input type="hidden" name="temporary_address_id" value="<?= $student['temporary_address_id'] ?>">

        <!-- Profile Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Profile</div>
            <div class="card-body">
                <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" class="form-control mb-2" required>
                <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" class="form-control mb-2" required>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" class="form-control mb-2" required>
                <input type="text" name="varsity_id" value="<?= htmlspecialchars($student['varsity_id']) ?>" class="form-control mb-2" required>

                <select name="gender" class="form-control mb-2">
                    <option value="">-- Select Gender --</option>
                    <option value="male" <?= $student['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $student['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $student['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>

                <input type="text" name="contact_number" value="<?= htmlspecialchars($student['contact_number']) ?>" class="form-control mb-2">
                <input type="text" name="emergency_contact" value="<?= htmlspecialchars($student['emergency_contact']) ?>" class="form-control mb-2">
            </div>
        </div>

        <!-- Parent Details -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Parent Details</div>
            <div class="card-body">
                <input type="text" name="father_name" value="<?= htmlspecialchars($student['father_name']) ?>" class="form-control mb-2">
                <input type="text" name="father_contact" value="<?= htmlspecialchars($student['father_contact']) ?>" class="form-control mb-2">
                <input type="text" name="mother_name" value="<?= htmlspecialchars($student['mother_name']) ?>" class="form-control mb-2">
                <input type="text" name="mother_contact" value="<?= htmlspecialchars($student['mother_contact']) ?>" class="form-control mb-2">
            </div>
        </div>

        <!-- Hostel Info -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Hostel & Room</div>
            <div class="card-body">
                <select name="hostel_id" id="hostel-select" class="form-select mb-2" required>
                    <option value="">-- Select Hostel --</option>
                    <?php foreach ($hostels as $hostel): ?>
                        <option value="<?= $hostel['id'] ?>" <?= $hostel['id'] == $student['hostel_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hostel['hostel_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="floor_id" id="floor-select" class="form-select mb-2">
                    <option value="">-- Select Floor --</option>
                    <?php foreach ($floors as $floor): ?>
                        <option value="<?= $floor['id'] ?>" <?= $floor['id'] == $student['floor_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($floor['floor_name'] ?? 'Floor ' . $floor['floor_number']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="room_id" id="room-select" class="form-select mb-3">
                    <option value="">-- Select Room --</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['id'] ?>" <?= $room['id'] == $student['room_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($room['room_number']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="form-check mb-2">
                    <input type="checkbox" name="is_checked_in" value="1" <?= $student['is_checked_in'] ? 'checked' : '' ?> class="form-check-input">
                    <label class="form-check-label">Checked In</label>
                </div>

                <input type="datetime-local" name="check_in_at" value="<?= $student['check_in_at'] ? date('Y-m-d\TH:i', strtotime($student['check_in_at'])) : '' ?>" class="form-control mb-2">
                <input type="datetime-local" name="check_out_at" value="<?= $student['check_out_at'] ? date('Y-m-d\TH:i', strtotime($student['check_out_at'])) : '' ?>" class="form-control mb-2">
            </div>
        </div>

        <!-- Verification -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Approval & Verification</div>
            <div class="card-body">
                <div class="form-check">
                    <input type="checkbox" name="is_verified" value="1" <?= $student['is_verified'] ? 'checked' : '' ?> class="form-check-input">
                    <label class="form-check-label">Verified</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_approved" value="1" <?= $student['is_approved'] ? 'checked' : '' ?> class="form-check-input">
                    <label class="form-check-label">Approved</label>
                </div>
            </div>
        </div>

        <!-- Permanent Address -->
        <!-- Permanent Address -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Permanent Address</div>
            <div class="card-body">
                <select name="perm_country_id" class="form-control mb-2">
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['id'] ?>" <?= $country['id'] == $student['perm_country_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country['country_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="perm_state" value="<?= htmlspecialchars($student['perm_state'] ?? '') ?>" class="form-control mb-2" placeholder="State">
                <input type="text" name="perm_division" value="<?= htmlspecialchars($student['perm_division'] ?? '') ?>" class="form-control mb-2" placeholder="Division">
                <input type="text" name="perm_district" value="<?= htmlspecialchars($student['perm_district'] ?? '') ?>" class="form-control mb-2" placeholder="District">
                <input type="text" name="perm_sub_district" value="<?= htmlspecialchars($student['perm_sub_district'] ?? '') ?>" class="form-control mb-2" placeholder="Sub-district">
                <input type="text" name="perm_village" value="<?= htmlspecialchars($student['perm_village'] ?? '') ?>" class="form-control mb-2" placeholder="Village">
                <input type="text" name="perm_postalcode" value="<?= htmlspecialchars($student['perm_postalcode'] ?? '') ?>" class="form-control mb-2" placeholder="Postal Code">
                <input type="text" name="perm_street" value="<?= htmlspecialchars($student['perm_street'] ?? '') ?>" class="form-control mb-2" placeholder="Street">
                <input type="text" name="perm_house_no" value="<?= htmlspecialchars($student['perm_house_no'] ?? '') ?>" class="form-control mb-2" placeholder="House No">
                <textarea name="perm_detail" class="form-control mb-2" placeholder="Additional Detail"><?= htmlspecialchars($student['perm_detail'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Copy Address Checkbox -->
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="same-address">
            <label class="form-check-label" for="same-address">Same as permanent address</label>
        </div>

        <!-- Temporary Address -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Temporary Address</div>
            <div class="card-body">
                <select name="temp_country_id" class="form-control mb-2">
                    <option value="">-- Select Country --</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['id'] ?>" <?= $country['id'] == $student['temp_country_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($country['country_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="temp_state" value="<?= htmlspecialchars($student['temp_state'] ?? '') ?>" class="form-control mb-2" placeholder="State">
                <input type="text" name="temp_division" value="<?= htmlspecialchars($student['temp_division'] ?? '') ?>" class="form-control mb-2" placeholder="Division">
                <input type="text" name="temp_district" value="<?= htmlspecialchars($student['temp_district'] ?? '') ?>" class="form-control mb-2" placeholder="District">
                <input type="text" name="temp_sub_district" value="<?= htmlspecialchars($student['temp_sub_district'] ?? '') ?>" class="form-control mb-2" placeholder="Sub-district">
                <input type="text" name="temp_village" value="<?= htmlspecialchars($student['temp_village'] ?? '') ?>" class="form-control mb-2" placeholder="Village">
                <input type="text" name="temp_postalcode" value="<?= htmlspecialchars($student['temp_postalcode'] ?? '') ?>" class="form-control mb-2" placeholder="Postal Code">
                <input type="text" name="temp_street" value="<?= htmlspecialchars($student['temp_street'] ?? '') ?>" class="form-control mb-2" placeholder="Street">
                <input type="text" name="temp_house_no" value="<?= htmlspecialchars($student['temp_house_no'] ?? '') ?>" class="form-control mb-2" placeholder="House No">
                <textarea name="temp_detail" class="form-control mb-2" placeholder="Additional Detail"><?= htmlspecialchars($student['temp_detail'] ?? '') ?></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mb-3">Update Student</button>
    </form>

    <div id="showMessage" class="alert d-none mt-3"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Load floors when hostel is selected
            $('#hostel-select').on('change', function() {
                var hostelId = $(this).val();
                var $floorSelect = $('#floor-select');

                $floorSelect.html('<option value="">Loading...</option>');

                $.ajax({
                    url: "<?= BASE_URL . '/admin/sections/students/get_floors.php' ?>",
                    method: 'GET',
                    data: {
                        hostel_id: hostelId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $floorSelect.html('<option value="">-- Select Floor --</option>');
                        $.each(data, function(index, floor) {
                            var floorName = floor.floor_name ? floor.floor_name + ' (Floor ' + floor.floor_number + ')' : 'Floor ' + floor.floor_number;
                            $floorSelect.append(
                                $('<option>').val(floor.id).text(floorName)
                            );
                        });
                    },
                    error: function() {
                        $floorSelect.html('<option value="">Error loading floors</option>');
                    }
                });
            });

            // Load rooms when floor is selected
            $('#floor-select').on('change', function() {
                var floorId = $(this).val();
                var $roomSelect = $('#room-select');

                $roomSelect.html('<option value="">Loading...</option>');
                $.ajax({
                    url: "<?= BASE_URL . '/admin/sections/students/get_rooms.php' ?>",
                    method: 'GET',
                    data: {
                        floor_id: floorId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $roomSelect.html('<option value="">-- Select Room --</option>');
                        $.each(data, function(index, room) {
                            $roomSelect.append(
                                $('<option>').val(room.id).text(room.room_number)
                            );
                        });
                    },
                    error: function() {
                        $roomSelect.html('<option value="">Error loading rooms</option>');
                    }
                });
            });

            // Copy permanent address to temporary address
            $('#same-address').on('change', function() {
                if (this.checked) {
                    $('select[name="temp_country_id"]').val($('select[name="perm_country_id"]').val());
                    $('input[name="temp_state"]').val($('input[name="perm_state"]').val());
                    $('input[name="temp_division"]').val($('input[name="perm_division"]').val());
                    $('input[name="temp_district"]').val($('input[name="perm_district"]').val());
                    $('input[name="temp_sub_district"]').val($('input[name="perm_sub_district"]').val());
                    $('input[name="temp_village"]').val($('input[name="perm_village"]').val());
                    $('input[name="temp_postalcode"]').val($('input[name="perm_postalcode"]').val());
                    $('input[name="temp_street"]').val($('input[name="perm_street"]').val());
                    $('input[name="temp_house_no"]').val($('input[name="perm_house_no"]').val());
                    $('textarea[name="temp_detail"]').val($('textarea[name="perm_detail"]').val());
                } else {
                    // Clear temporary address fields
                    $('select[name="temp_country_id"]').val('');
                    $('input[name="temp_state"], input[name="temp_division"], input[name="temp_district"], input[name="temp_sub_district"], input[name="temp_village"], input[name="temp_postalcode"], input[name="temp_street"], input[name="temp_house_no"]').val('');
                    $('textarea[name="temp_detail"]').val('');
                }
            });

            // Form submission
            $('#edit-student-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var formData = new FormData(this);

                // Show loading state
                $('#showMessage').html('')
                    .removeClass('alert-danger alert-success')
                    .addClass('alert-info')
                    .html('<div class="spinner-border spinner-border-sm" role="status"></div> Updating student...')
                    .removeClass('d-none');

                $.ajax({
                    url: '<?= BASE_URL . '/admin/php_files/sections/students/edit.php' ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        var messageDiv = $('#showMessage')
                            .removeClass('alert-info alert-danger')
                            .addClass(response.success ? 'alert-success' : 'alert-danger')
                            .html(response.message);

                        if (response.success) {
                            setTimeout(function() {
                                messageDiv.fadeOut();
                            }, 3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#showMessage')
                            .removeClass('alert-info alert-success')
                            .addClass('alert-danger')
                            .html('Server error: ' + error);
                    }
                });
            });
        });
    </script>

    <?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>