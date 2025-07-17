<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Helper to sanitize input
function sanitize($data, $conn) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Handle file upload
$profileImagePath = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = BASE_PATH . '/uploads/profile_images/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $filename = uniqid('profile_', true) . '_' . basename($_FILES['profile_image']['name']);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
        $profileImagePath = 'uploads/profile_images/' . $filename;
    }
}

// 1. Insert permanent address
$permFields = [
    'perm_country_id', 'division', 'district', 'sub_district', 'village',
    'postalcode', 'street', 'house_no', 'detail'
];
$permData = array_map(fn($f) => sanitize($_POST[$f] ?? '', $conn), $permFields);
$conn->query("INSERT INTO addresses (country_id, division, district, sub_district, village, postal_code, street, house_no, detail)
              VALUES ('$permData[0]', '$permData[1]', '$permData[2]', '$permData[3]', '$permData[4]', '$permData[5]', '$permData[6]', '$permData[7]', '$permData[8]')");
$permAddressId = $conn->insert_id;

// 2. Insert temporary address
$tempFields = [
    'temp_country_id', 'division_1', 'district_1', 'sub_district_1', 'village_1',
    'postalcode_1', 'street_1', 'house_no_1', 'detail_1'
];
$tempData = array_map(fn($f) => sanitize($_POST[$f] ?? '', $conn), $tempFields);
$conn->query("INSERT INTO addresses (country_id, division, district, sub_district, village, postal_code, street, house_no, detail)
              VALUES ('$tempData[0]', '$tempData[1]', '$tempData[2]', '$tempData[3]', '$tempData[4]', '$tempData[5]', '$tempData[6]', '$tempData[7]', '$tempData[8]')");
$tempAddressId = $conn->insert_id;

// 3. Insert student
$firstName = sanitize($_POST['first_name'], $conn);
$lastName = sanitize($_POST['last_name'], $conn);
$email = sanitize($_POST['email'], $conn);
$varsityId = sanitize($_POST['varsity_id'], $conn);
$gender = sanitize($_POST['gender'], $conn);
$contactNumber = sanitize($_POST['contact_number'], $conn);
$emergencyContact = sanitize($_POST['emergency_contact'], $conn);
$fatherName = sanitize($_POST['father_name'], $conn);
$fatherContact = sanitize($_POST['father_contact'], $conn);
$motherName = sanitize($_POST['mother_name'], $conn);
$motherContact = sanitize($_POST['mother_contact'], $conn);
$hostelId = (int)($_POST['hostel_id'] ?? 0);
$floorId = (int)($_POST['floor_id'] ?? 0);
$roomId = (int)($_POST['room_id'] ?? 0);
$isCheckedIn = isset($_POST['is_checked_in']) ? 1 : 0;
$checkInAt = !empty($_POST['check_in_at']) ? "'" . sanitize($_POST['check_in_at'], $conn) . "'" : "NULL";
$checkOutAt = !empty($_POST['check_out_at']) ? "'" . sanitize($_POST['check_out_at'], $conn) . "'" : "NULL";
$isVerified = isset($_POST['is_verified']) ? 1 : 0;
$isApproved = isset($_POST['is_approved']) ? 1 : 0;
$verificationToken = sanitize($_POST['verification_token'] ?? '', $conn);

$conn->query("
    INSERT INTO students (
        first_name, last_name, email, varsity_id, gender, contact_number, emergency_contact,
        profile_image, father_name, father_contact, mother_name, mother_contact,
        hostel_id, floor_id, room_id, is_checked_in, check_in_at, check_out_at,
        is_verified, is_approved, verification_token,
        perm_address_id, temp_address_id
    ) VALUES (
        '$firstName', '$lastName', '$email', '$varsityId', '$gender', '$contactNumber', '$emergencyContact',
        '$profileImagePath', '$fatherName', '$fatherContact', '$motherName', '$motherContact',
        $hostelId, $floorId, $roomId, $isCheckedIn, $checkInAt, $checkOutAt,
        $isVerified, $isApproved, '$verificationToken',
        $permAddressId, $tempAddressId
    )
");

if ($conn->affected_rows > 0) {
    header("Location: student_list.php?success=1");
    exit;
} else {
    echo "Error saving student: " . $conn->error;
}
?>
