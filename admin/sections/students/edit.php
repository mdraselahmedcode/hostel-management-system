<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/response_helper.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';

require_admin();

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

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .header {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            margin-top: 2rem;
        }

        .card-header {
            font-weight: 600;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <?php
        require_once BASE_PATH . '/admin/includes/header_admin.php';
        ?>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <h2 class="mb-0 text-primary">
                <i class="fas fa-user-edit me-2"></i>Edit Student
            </h2>
            <div></div> <!-- Empty div for alignment -->
        </div>

        <form id="edit-student-form" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="id" value="<?= $student['id'] ?>">
            <input type="hidden" name="permanent_address_id" value="<?= $student['permanent_address_id'] ?>">
            <input type="hidden" name="temporary_address_id" value="<?= $student['temporary_address_id'] ?>">

            <!-- Profile Section -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-id-card me-2"></i>Profile Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label required-field">First Name</label>
                            <input type="text" name="first_name" id="first_name"
                                value="<?= htmlspecialchars($student['first_name']) ?>"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label required-field">Last Name</label>
                            <input type="text" name="last_name" id="last_name"
                                value="<?= htmlspecialchars($student['last_name']) ?>"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label required-field">Email</label>
                            <input type="email" name="email" id="email"
                                value="<?= htmlspecialchars($student['email']) ?>"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="varsity_id" class="form-label required-field">Varsity ID</label>
                            <input type="text" name="varsity_id" id="varsity_id"
                                value="<?= htmlspecialchars($student['varsity_id']) ?>"
                                class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label required-field">Gender</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="">-- Select Gender --</option>
                                <option value="male" <?= $student['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= $student['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= $student['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number"
                                value="<?= htmlspecialchars($student['contact_number']) ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="emergency_contact" class="form-label">Emergency Contact</label>
                            <input type="text" name="emergency_contact" id="emergency_contact"
                                value="<?= htmlspecialchars($student['emergency_contact']) ?>"
                                class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent Details -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-users me-2"></i>Parent Details
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="father_name" class="form-label">Father's Name</label>
                            <input type="text" name="father_name" id="father_name"
                                value="<?= htmlspecialchars($student['father_name']) ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="father_contact" class="form-label">Father's Contact</label>
                            <input type="text" name="father_contact" id="father_contact"
                                value="<?= htmlspecialchars($student['father_contact']) ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="mother_name" class="form-label">Mother's Name</label>
                            <input type="text" name="mother_name" id="mother_name"
                                value="<?= htmlspecialchars($student['mother_name']) ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="mother_contact" class="form-label">Mother's Contact</label>
                            <input type="text" name="mother_contact" id="mother_contact"
                                value="<?= htmlspecialchars($student['mother_contact']) ?>"
                                class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hostel Info -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-bed me-2"></i>Hostel Information
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="hostel-select" class="form-label required-field">Hostel</label>
                            <select name="hostel_id" id="hostel-select" class="form-select" required>
                                <option value="">-- Select Hostel --</option>
                                <?php foreach ($hostels as $hostel): ?>
                                    <option value="<?= $hostel['id'] ?>" <?= $hostel['id'] == $student['hostel_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hostel['hostel_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="floor-select" class="form-label">Floor</label>
                            <select name="floor_id" id="floor-select" class="form-select">
                                <option value="">-- Select Floor --</option>
                                <?php foreach ($floors as $floor): ?>
                                    <option value="<?= $floor['id'] ?>" <?= $floor['id'] == $student['floor_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($floor['floor_name'] ?? 'Floor ' . $floor['floor_number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="room-select" class="form-label">Room</label>
                            <select name="room_id" id="room-select" class="form-select">
                                <option value="">-- Select Room --</option>
                                <?php foreach ($rooms as $room): ?>
                                    <option value="<?= $room['id'] ?>" <?= $room['id'] == $student['room_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($room['room_number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch pt-4">
                                <input type="checkbox" name="is_checked_in" id="is_checked_in" value="1"
                                    <?= $student['is_checked_in'] ? 'checked' : '' ?> class="form-check-input">
                                <label class="form-check-label" for="is_checked_in">Checked In</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="check_in_at" class="form-label">Check In Time</label>
                            <input type="datetime-local" name="check_in_at" id="check_in_at"
                                value="<?= $student['check_in_at'] ? date('Y-m-d\TH:i', strtotime($student['check_in_at'])) : '' ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="check_out_at" class="form-label">Check Out Time</label>
                            <input type="datetime-local" name="check_out_at" id="check_out_at"
                                value="<?= $student['check_out_at'] ? date('Y-m-d\TH:i', strtotime($student['check_out_at'])) : '' ?>"
                                class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-shield-alt me-2"></i>Verification Status
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_verified" id="is_verified" value="1"
                                    <?= $student['is_verified'] ? 'checked' : '' ?> class="form-check-input">
                                <label class="form-check-label" for="is_verified">Verified</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_approved" id="is_approved" value="1"
                                    <?= $student['is_approved'] ? 'checked' : '' ?> class="form-check-input">
                                <label class="form-check-label" for="is_approved">Approved</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permanent Address -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-home me-2"></i>Permanent Address
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="perm_country_id" class="form-label">Country</label>
                            <select name="perm_country_id" id="perm_country_id" class="form-select">
                                <option value="">-- Select Country --</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= $country['id'] ?>" <?= $country['id'] == $student['perm_country_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($country['country_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="perm_state" class="form-label">State</label>
                            <input type="text" name="perm_state" id="perm_state"
                                value="<?= htmlspecialchars($student['perm_state'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_division" class="form-label">Division</label>
                            <input type="text" name="perm_division" id="perm_division"
                                value="<?= htmlspecialchars($student['perm_division'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_district" class="form-label">District</label>
                            <input type="text" name="perm_district" id="perm_district"
                                value="<?= htmlspecialchars($student['perm_district'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_sub_district" class="form-label">Sub-district</label>
                            <input type="text" name="perm_sub_district" id="perm_sub_district"
                                value="<?= htmlspecialchars($student['perm_sub_district'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_village" class="form-label">Village</label>
                            <input type="text" name="perm_village" id="perm_village"
                                value="<?= htmlspecialchars($student['perm_village'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_postalcode" class="form-label">Postal Code</label>
                            <input type="text" name="perm_postalcode" id="perm_postalcode"
                                value="<?= htmlspecialchars($student['perm_postalcode'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_street" class="form-label">Street</label>
                            <input type="text" name="perm_street" id="perm_street"
                                value="<?= htmlspecialchars($student['perm_street'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="perm_house_no" class="form-label">House No</label>
                            <input type="text" name="perm_house_no" id="perm_house_no"
                                value="<?= htmlspecialchars($student['perm_house_no'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="perm_detail" class="form-label">Additional Details</label>
                            <textarea name="perm_detail" id="perm_detail" class="form-control"><?= htmlspecialchars($student['perm_detail'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copy Address Checkbox -->
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="same-address">
                <label class="form-check-label" for="same-address">Same as permanent address</label>
            </div>

            <!-- Temporary Address -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-map-marker-alt me-2"></i>Temporary Address
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="temp_country_id" class="form-label">Country</label>
                            <select name="temp_country_id" id="temp_country_id" class="form-select">
                                <option value="">-- Select Country --</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= $country['id'] ?>" <?= $country['id'] == $student['temp_country_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($country['country_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="temp_state" class="form-label">State</label>
                            <input type="text" name="temp_state" id="temp_state"
                                value="<?= htmlspecialchars($student['temp_state'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_division" class="form-label">Division</label>
                            <input type="text" name="temp_division" id="temp_division"
                                value="<?= htmlspecialchars($student['temp_division'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_district" class="form-label">District</label>
                            <input type="text" name="temp_district" id="temp_district"
                                value="<?= htmlspecialchars($student['temp_district'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_sub_district" class="form-label">Sub-district</label>
                            <input type="text" name="temp_sub_district" id="temp_sub_district"
                                value="<?= htmlspecialchars($student['temp_sub_district'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_village" class="form-label">Village</label>
                            <input type="text" name="temp_village" id="temp_village"
                                value="<?= htmlspecialchars($student['temp_village'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_postalcode" class="form-label">Postal Code</label>
                            <input type="text" name="temp_postalcode" id="temp_postalcode"
                                value="<?= htmlspecialchars($student['temp_postalcode'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_street" class="form-label">Street</label>
                            <input type="text" name="temp_street" id="temp_street"
                                value="<?= htmlspecialchars($student['temp_street'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="temp_house_no" class="form-label">House No</label>
                            <input type="text" name="temp_house_no" id="temp_house_no"
                                value="<?= htmlspecialchars($student['temp_house_no'] ?? '') ?>"
                                class="form-control">
                        </div>
                        <div class="col-12">
                            <label for="temp_detail" class="form-label">Additional Details</label>
                            <textarea name="temp_detail" id="temp_detail" class="form-control"><?= htmlspecialchars($student['temp_detail'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> Update Student
                </button>
            </div>
        </form>

    </main>

    <!-- Footer -->
    <?php
    require_once BASE_PATH . '/admin/includes/footer_admin.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    $('select[name="temp_country_id"]').val('<?= $student['temp_country_id'] ?? '' ?>');
                    $('input[name="temp_state"]').val('<?= htmlspecialchars($student['temp_state'] ?? '') ?>');
                    $('input[name="temp_division"]').val('<?= htmlspecialchars($student['temp_division'] ?? '') ?>');
                    $('input[name="temp_district"]').val('<?= htmlspecialchars($student['temp_district'] ?? '') ?>');
                    $('input[name="temp_sub_district"]').val('<?= htmlspecialchars($student['temp_sub_district'] ?? '') ?>');
                    $('input[name="temp_village"]').val('<?= htmlspecialchars($student['temp_village'] ?? '') ?>');
                    $('input[name="temp_postalcode"]').val('<?= htmlspecialchars($student['temp_postalcode'] ?? '') ?>');
                    $('input[name="temp_street"]').val('<?= htmlspecialchars($student['temp_street'] ?? '') ?>');
                    $('input[name="temp_house_no"]').val('<?= htmlspecialchars($student['temp_house_no'] ?? '') ?>');
                    $('textarea[name="temp_detail"]').val('<?= htmlspecialchars($student['temp_detail'] ?? '') ?>');
                }
            });

            // Form submission
            $('#edit-student-form').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                var originalBtnText = submitBtn.html();

                // Show loading state
                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...'
                );

                // Optional loading message
                showSlideMessage('Updating student information...', 'info');

                $.ajax({
                    url: '<?= BASE_URL . '/admin/php_files/sections/students/edit.php' ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showSlideMessage('<i class="fas fa-check-circle me-2"></i>' + response.message, 'success');

                            // Redirect after 2.5 seconds (adjust if needed)
                            setTimeout(function() {
                                window.location.href = response.redirect || '<?= BASE_URL . "/admin/sections/students/index.php" ?>';
                            }, 2500);
                        } else {
                            showSlideMessage('<i class="fas fa-exclamation-circle me-2"></i>' + (response.message || 'Update failed.'), 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        showSlideMessage('<i class="fas fa-times-circle me-2"></i> Server error: ' + error, 'danger');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                });
            });

        });
    </script>
</body>

</html>