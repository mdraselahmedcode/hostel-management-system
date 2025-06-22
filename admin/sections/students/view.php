<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid student ID.";
    exit;
}

$studentId = intval($_GET['id']);

$sql = "
    SELECT 
        students.*,
        hostels.hostel_name,
        rooms.room_number,
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
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Details - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-4">

    <h2>Student Details</h2>
    <a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

    <div class="card mb-4">
        <div class="card-header bg-primary text-light">Profile</div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($student['email']) ?><br>
                <strong>Varsity ID:</strong> <?= htmlspecialchars($student['varsity_id']) ?><br>
                <strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?><br>
                <strong>Contact:</strong> <?= htmlspecialchars($student['contact_number']) ?><br>
                <strong>Emergency Contact:</strong> <?= htmlspecialchars($student['emergency_contact']) ?><br>
                <strong>Profile Image:</strong><br>
                <?php if ($student['profile_image_url']): ?>
                    <img src="<?= htmlspecialchars($student['profile_image_url']) ?>" alt="Profile Image" width="120">
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header bg-primary text-light">Parent Details</div>
        <div class="card-body">
            <strong>Father's Name:</strong> <?= htmlspecialchars($student['father_name'] ?? 'N/A') ?><br>
            <strong>Father's Contact:</strong> <?= htmlspecialchars($student['father_contact'] ?? 'N/A') ?><br>
            <strong>Mother's Name:</strong> <?= htmlspecialchars($student['mother_name'] ?? 'N/A') ?><br>
            <strong>Mother's Contact:</strong> <?= htmlspecialchars($student['mother_contact'] ?? 'N/A') ?><br>
            <strong>Emergency Contact:</strong> <?= htmlspecialchars($student['emergency_contact'] ?? 'N/A') ?><br>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header bg-primary text-light">Hostel & Room</div>
        <div class="card-body">
            <strong>Hostel:</strong> <?= htmlspecialchars($student['hostel_name'] ?? 'N/A') ?><br>
            <strong>Room:</strong> <?= htmlspecialchars($student['room_number'] ?? 'N/A') ?><br>
            <strong>Floor Number:</strong> <?= htmlspecialchars($student['floor_number'] ?? 'N/A') ?><br>
            <strong>Floor Name:</strong> <?= htmlspecialchars($student['floor_name'] ?? 'N/A') ?><br>
            <strong>Checked In:</strong> <?= $student['is_checked_in'] ? 'Yes' : 'No' ?><br>
            <strong>Check In Time:</strong> <?= $student['check_in_at'] ?? 'N/A' ?><br>
            <strong>Check Out Time:</strong> <?= $student['check_out_at'] ?? 'N/A' ?><br>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header bg-primary text-light">Approval & Verification</div>
        <div class="card-body">
            <strong>Verified:</strong> <?= $student['is_verified'] ? 'Yes' : 'No' ?><br>
            <strong>Approved:</strong> <?= $student['is_approved'] ? 'Yes' : 'No' ?><br>
            <strong>Verification Token:</strong> <?= htmlspecialchars($student['verification_token'] ?? 'N/A') ?><br>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-light">Permanent Address</div>
        <div class="card-body">
            <strong>Country:</strong> <?= htmlspecialchars($student['perm_country_name'] ?? 'N/A') ?><br>
            <strong>Division:</strong> <?= htmlspecialchars($student['division'] ?? '') ?><br>
            <strong>District:</strong> <?= htmlspecialchars($student['district'] ?? '') ?><br>
            <strong>Sub-district:</strong> <?= htmlspecialchars($student['sub_district'] ?? '') ?><br>
            <strong>Village:</strong> <?= htmlspecialchars($student['village'] ?? '') ?><br>
            <strong>Postal Code:</strong> <?= htmlspecialchars($student['postalcode'] ?? '') ?><br>
            <strong>Street:</strong> <?= htmlspecialchars($student['street'] ?? '') ?><br>
            <strong>House No:</strong> <?= htmlspecialchars($student['house_no'] ?? '') ?><br>
            <strong>Detail:</strong> <?= htmlspecialchars($student['detail'] ?? '') ?><br>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-light">Temporary Address</div>
        <div class="card-body">
            <strong>Country:</strong> <?= htmlspecialchars($student['temp_country_name'] ?? 'N/A') ?><br>
            <strong>State:</strong> <?= htmlspecialchars($student['temp_state'] ?? '') ?><br>
            <strong>Division:</strong> <?= htmlspecialchars($student['temp_division'] ?? '') ?><br>
            <strong>District:</strong> <?= htmlspecialchars($student['temp_district'] ?? '') ?><br>
            <strong>Sub-district:</strong> <?= htmlspecialchars($student['temp_sub_district'] ?? '') ?><br>
            <strong>Village:</strong> <?= htmlspecialchars($student['temp_village'] ?? '') ?><br>
            <strong>Postal Code:</strong> <?= htmlspecialchars($student['temp_postalcode'] ?? '') ?><br>
            <strong>Street:</strong> <?= htmlspecialchars($student['temp_street'] ?? '') ?><br>
            <strong>House No:</strong> <?= htmlspecialchars($student['temp_house_no'] ?? '') ?><br>
            <strong>Detail:</strong> <?= htmlspecialchars($student['temp_detail'] ?? '') ?><br>
        </div>
    </div>




</body>

</html>

<?php
$stmt->close();
$conn->close();
?>