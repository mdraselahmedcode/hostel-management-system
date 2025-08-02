<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid student ID.";
    exit;
}

$studentId = intval($_GET['id']);

// $sql = "
//     SELECT 
//         students.*,
//         hostels.hostel_name,
//         rooms.room_number,
//         floors.floor_number,
//         floors.floor_name,
//         perm_addr.*, 
//         perm_country.country_name AS perm_country_name,
//         temp_addr.id AS temp_addr_id,
//         temp_addr.country_id AS temp_country_id,
//         temp_addr.state AS temp_state,
//         temp_addr.division AS temp_division,
//         temp_addr.district AS temp_district,
//         temp_addr.sub_district AS temp_sub_district,
//         temp_addr.village AS temp_village,
//         temp_addr.postalcode AS temp_postalcode,
//         temp_addr.street AS temp_street,
//         temp_addr.house_no AS temp_house_no,
//         temp_addr.detail AS temp_detail,
//         temp_country.country_name AS temp_country_name
//     FROM students
//     LEFT JOIN hostels ON students.hostel_id = hostels.id
//     LEFT JOIN rooms ON students.room_id = rooms.id
//     LEFT JOIN floors ON rooms.floor_id = floors.id
//     LEFT JOIN addresses AS perm_addr ON students.permanent_address_id = perm_addr.id
//     LEFT JOIN countries AS perm_country ON perm_addr.country_id = perm_country.id
//     LEFT JOIN addresses AS temp_addr ON students.temporary_address_id = temp_addr.id
//     LEFT JOIN countries AS temp_country ON temp_addr.country_id = temp_country.id
//     WHERE students.id = ?
// ";

$sql = "
    SELECT 
        students.*,
        hostels.hostel_name,
        rooms.room_number,
        room_types.type_name AS room_type,
        floors.floor_number,
        floors.floor_name,
        perm_addr.*, 
        perm_country.country_name AS perm_country_name,
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
        temp_country.country_name AS temp_country_name,
        sp.id AS payment_id
    FROM students
    LEFT JOIN hostels ON students.hostel_id = hostels.id
    LEFT JOIN rooms ON students.room_id = rooms.id
    LEFT JOIN room_types ON rooms.room_type_id = room_types.id
    LEFT JOIN floors ON rooms.floor_id = floors.id
    LEFT JOIN addresses AS perm_addr ON students.permanent_address_id = perm_addr.id
    LEFT JOIN countries AS perm_country ON perm_addr.country_id = perm_country.id
    LEFT JOIN addresses AS temp_addr ON students.temporary_address_id = temp_addr.id
    LEFT JOIN countries AS temp_country ON temp_addr.country_id = temp_country.id
    LEFT JOIN student_payments AS sp ON sp.student_id = students.id
    WHERE students.id = ?
    LIMIT 1
";


$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Student not found.";
    exit;
}

$student = $result->fetch_assoc();
$payment_id = $student['payment_id'];


?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
    <link rel="stylesheet" href="<?= BASE_PATH . '/vendor/bootstrap/css/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .header {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            position: sticky;
            top: 0;
            left: 0;
            z-index: 1000;

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

        .profile-img {
            max-width: 150px;
            border-radius: 5px;
        }
    </style>
</head>
<div class="header">
    <?php
    require_once BASE_PATH . '/admin/includes/header_admin.php';
    ?>
</div>
<!-- Main Content -->
<main class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <h2 class="mb-0 text-primary">
            <i class="fas fa-id-card me-2"></i>Student Details
        </h2>
        <div></div> <!-- Empty div for alignment -->
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Profile Information</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <dl class="row">
                        <dt class="col-sm-4">Full Name</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['email']) ?></dd>

                        <dt class="col-sm-4">Varsity ID</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['varsity_id']) ?></dd>

                        <dt class="col-sm-4">Gender</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['gender']) ?></dd>

                        <dt class="col-sm-4">Blood Group</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['blood_group']) ?></dd>

                        <dt class="col-sm-4">Contact Number</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['contact_number']) ?></dd>

                        <dt class="col-sm-4">Emergency Contact</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($student['emergency_contact']) ?></dd>

                        <dt class="col-sm-4">View Payment</dt>
                        <dd class="col-sm-8">
                            <?php if (!empty($payment_id)) : ?>
                                <a href="<?= BASE_URL ?>/admin/sections/payments/view.php?id=<?= $payment_id ?>" class="btn btn-sm btn-outline-primary">View</a>
                            <?php else : ?>
                                <span class="text-muted">No payment found</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-4 text-center">
                    <?php if ($student['profile_image_url']): ?>
                        <img src="<?= htmlspecialchars($student['profile_image_url']) ?>" alt="Profile Image" class="profile-img img-thumbnail">
                    <?php else: ?>
                        <div class="text-muted">No profile image</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Parent Details</div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Father's Name</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($student['father_name'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5">Father's Contact</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($student['father_contact'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5">Mother's Name</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($student['mother_name'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5">Mother's Contact</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($student['mother_contact'] ?? 'N/A') ?></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Hostel Information</div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Hostel</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($student['hostel_name'] ?? 'N/A') ?></dd>

                        <dt class="col-sm-5">Room Number</dt>
                        <dd class="col-sm-7">
                            <?= htmlspecialchars(
                                !empty($student['room_number']) && !empty($student['room_type'])
                                    ? $student['room_number'] . ' (' . $student['room_type'] . ')'
                                    : 'N/A'
                            ) ?>
                        </dd>

                        <dt class="col-sm-5">Floor</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($student['floor_number'] ?? 'N/A') ?> (<?= htmlspecialchars($student['floor_name'] ?? 'N/A') ?>)</dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7"><?= $student['is_checked_in'] ? 'Checked In' : 'Checked Out' ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Permanent Address</div>
                <div class="card-body">
                    <address>
                        <?= htmlspecialchars($student['house_no'] ?? '') ?>, <?= htmlspecialchars($student['street'] ?? '') ?><br>
                        <?= htmlspecialchars($student['village'] ?? '') ?>, <?= htmlspecialchars($student['sub_district'] ?? '') ?><br>
                        <?= htmlspecialchars($student['district'] ?? '') ?>, <?= htmlspecialchars($student['division'] ?? '') ?><br>
                        <?= htmlspecialchars($student['perm_country_name'] ?? 'N/A') ?><br>
                        Postal Code: <?= htmlspecialchars($student['postalcode'] ?? '') ?>
                    </address>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">Temporary Address</div>
                <div class="card-body">
                    <address>
                        <?= htmlspecialchars($student['temp_house_no'] ?? '') ?>, <?= htmlspecialchars($student['temp_street'] ?? '') ?><br>
                        <?= htmlspecialchars($student['temp_village'] ?? '') ?>, <?= htmlspecialchars($student['temp_sub_district'] ?? '') ?><br>
                        <?= htmlspecialchars($student['temp_district'] ?? '') ?>, <?= htmlspecialchars($student['temp_division'] ?? '') ?><br>
                        <?= htmlspecialchars($student['temp_country_name'] ?? 'N/A') ?><br>
                        Postal Code: <?= htmlspecialchars($student['temp_postalcode'] ?? '') ?>
                    </address>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">System Information</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Verified</dt>
                <dd class="col-sm-9"><?= $student['is_verified'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>' ?></dd>

                <dt class="col-sm-3">Approved</dt>
                <dd class="col-sm-9"><?= $student['is_approved'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>' ?></dd>

                <dt class="col-sm-3">Checked In</dt>
                <dd class="col-sm-9"><?= $student['check_in_at'] ?? 'N/A' ?></dd>

                <dt class="col-sm-3">Checked Out</dt>
                <dd class="col-sm-9"><?= $student['check_out_at'] ?? 'N/A' ?></dd>
            </dl>
        </div>
    </div>
</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?= date('Y') ?> Student Management System</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <a href="#" class="text-white me-3"><i class="fas fa-question-circle"></i> Help</a>
                    <a href="#" class="text-white"><i class="fas fa-envelope"></i> Contact</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="<?= BASE_PATH . '/vendor/bootstrap/js/bootstrap.bundle.min.js' ?>"></script>




<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
$stmt->close();
$conn->close();
?>