<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/response_helper.php';
// include BASE_PATH . '/student/php_files/send_email.php';    
include BASE_PATH . '/admin/php_files/sections/students/send_approved_mail.php';
include BASE_PATH . '/admin/php_files/sections/students/send_checkedin_mail.php';

// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Validate the Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

// Validate student ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid student ID'
    ]); 
    exit;
}

$student_id = intval($_POST['id']);

// Check if student exists
$checkStudent = $conn->prepare("SELECT id FROM students WHERE id = ?");
$checkStudent->bind_param("i", $student_id);
$checkStudent->execute();
$checkStudent->store_result();

if ($checkStudent->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Student not found'
    ]);
    exit;
}
$checkStudent->close();

// Sanitize and collect fields
$first_name = isset($_POST['first_name']) ? htmlspecialchars(trim($_POST['first_name'])) : null;
$last_name = isset($_POST['last_name']) ? htmlspecialchars(trim($_POST['last_name'])) : null;
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
$varsity_id = isset($_POST['varsity_id']) ? htmlspecialchars(trim($_POST['varsity_id'])) : null;
$gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : null;
$contact_number = isset($_POST['contact_number']) ? htmlspecialchars(trim($_POST['contact_number'])) : null;
$emergency_contact = isset($_POST['emergency_contact']) ? htmlspecialchars(trim($_POST['emergency_contact'])) : null;
$father_name = isset($_POST['father_name']) ? htmlspecialchars(trim($_POST['father_name'])) : null;
$father_contact = isset($_POST['father_contact']) ? htmlspecialchars(trim($_POST['father_contact'])) : null;
$mother_name = isset($_POST['mother_name']) ? htmlspecialchars(trim($_POST['mother_name'])) : null;
$mother_contact = isset($_POST['mother_contact']) ? htmlspecialchars(trim($_POST['mother_contact'])) : null;
$hostel_id = isset($_POST['hostel_id']) ? intval($_POST['hostel_id']) : null;
$floor_id = isset($_POST['floor_id']) ? intval($_POST['floor_id']) : null;
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : null;
$is_checked_in = isset($_POST['is_checked_in']) ? 1 : 0;
$check_in_at = !empty($_POST['check_in_at']) ? $_POST['check_in_at'] : null;
$check_out_at = !empty($_POST['check_out_at']) ? $_POST['check_out_at'] : null;
$is_verified = isset($_POST['is_verified']) ? 1 : 0;
$is_approved = isset($_POST['is_approved']) ? 1 : 0;

// Address IDs
$permanent_address_id = isset($_POST['permanent_address_id']) ? intval($_POST['permanent_address_id']) : null;
$temporary_address_id = isset($_POST['temporary_address_id']) ? intval($_POST['temporary_address_id']) : null;

// Permanent address fields
$perm_country_id = isset($_POST['perm_country_id']) ? intval($_POST['perm_country_id']) : null;
$perm_state = isset($_POST['perm_state']) ? htmlspecialchars(trim($_POST['perm_state'])) : null;
$perm_division = isset($_POST['perm_division']) ? htmlspecialchars(trim($_POST['perm_division'])) : null;
$perm_district = isset($_POST['perm_district']) ? htmlspecialchars(trim($_POST['perm_district'])) : null;
$perm_sub_district = isset($_POST['perm_sub_district']) ? htmlspecialchars(trim($_POST['perm_sub_district'])) : null;
$perm_village = isset($_POST['perm_village']) ? htmlspecialchars(trim($_POST['perm_village'])) : null;
$perm_postalcode = isset($_POST['perm_postalcode']) ? htmlspecialchars(trim($_POST['perm_postalcode'])) : null;
$perm_street = isset($_POST['perm_street']) ? htmlspecialchars(trim($_POST['perm_street'])) : null;
$perm_house_no = isset($_POST['perm_house_no']) ? htmlspecialchars(trim($_POST['perm_house_no'])) : null;
$perm_detail = isset($_POST['perm_detail']) ? htmlspecialchars(trim($_POST['perm_detail'])) : null;

// Temporary address fields
$temp_country_id = isset($_POST['temp_country_id']) ? intval($_POST['temp_country_id']) : null;
$temp_state = isset($_POST['temp_state']) ? htmlspecialchars(trim($_POST['temp_state'])) : null;
$temp_division = isset($_POST['temp_division']) ? htmlspecialchars(trim($_POST['temp_division'])) : null;
$temp_district = isset($_POST['temp_district']) ? htmlspecialchars(trim($_POST['temp_district'])) : null;
$temp_sub_district = isset($_POST['temp_sub_district']) ? htmlspecialchars(trim($_POST['temp_sub_district'])) : null;
$temp_village = isset($_POST['temp_village']) ? htmlspecialchars(trim($_POST['temp_village'])) : null;
$temp_postalcode = isset($_POST['temp_postalcode']) ? htmlspecialchars(trim($_POST['temp_postalcode'])) : null;
$temp_street = isset($_POST['temp_street']) ? htmlspecialchars(trim($_POST['temp_street'])) : null;
$temp_house_no = isset($_POST['temp_house_no']) ? htmlspecialchars(trim($_POST['temp_house_no'])) : null;
$temp_detail = isset($_POST['temp_detail']) ? htmlspecialchars(trim($_POST['temp_detail'])) : null;

// Validate required fields
$requiredFields = [
    'first_name', 'last_name', 'email', 'varsity_id', 'gender', 'contact_number', 'hostel_id', 'floor_id', 'room_id'
];
$missingFields = [];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $missingFields[] = $field;
    }
}
if (!empty($missingFields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields',
        'missing_fields' => $missingFields
    ]);
    exit;
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Check for unique email, varsity_id, contact_number
$checkUnique = $conn->prepare("
    SELECT id FROM students 
    WHERE (email = ? OR varsity_id = ? OR contact_number = ?) 
    AND id != ?
");
$checkUnique->bind_param("sssi", $email, $varsity_id, $contact_number, $student_id);
$checkUnique->execute();
$checkUnique->store_result();
if ($checkUnique->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'A student with the same email, varsity ID, or contact number already exists.'
    ]);
    exit;
}
$checkUnique->close();

// Validate hostel, floor, room hierarchy
$checkRoom = $conn->prepare("
    SELECT rooms.id FROM rooms
    LEFT JOIN floors ON rooms.floor_id = floors.id
    WHERE rooms.id = ? AND rooms.floor_id = ? AND floors.hostel_id = ?
");
$checkRoom->bind_param("iii", $room_id, $floor_id, $hostel_id);
$checkRoom->execute();
$checkRoom->store_result();
if ($checkRoom->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid room/floor/hostel combination.'
    ]); 
    exit;
}
$checkRoom->close();

// Update permanent address
if ($permanent_address_id) {
    $permAddrSql = "UPDATE addresses SET country_id=?, state=?, division=?, district=?, sub_district=?, village=?, postalcode=?, street=?, house_no=?, detail=? WHERE id=?";
    $permAddrStmt = $conn->prepare($permAddrSql);
    $permAddrStmt->bind_param(
        "isssssssssi",
        $perm_country_id, $perm_state, $perm_division, $perm_district, $perm_sub_district,
        $perm_village, $perm_postalcode, $perm_street, $perm_house_no, $perm_detail, $permanent_address_id
    );
    $permAddrStmt->execute();
    $permAddrStmt->close();
}

// Update temporary address
if ($temporary_address_id) {
    $tempAddrSql = "UPDATE addresses SET country_id=?, state=?, division=?, district=?, sub_district=?, village=?, postalcode=?, street=?, house_no=?, detail=? WHERE id=?";
    $tempAddrStmt = $conn->prepare($tempAddrSql);
    $tempAddrStmt->bind_param(
        "isssssssssi",
        $temp_country_id, $temp_state, $temp_division, $temp_district, $temp_sub_district,
        $temp_village, $temp_postalcode, $temp_street, $temp_house_no, $temp_detail, $temporary_address_id
    );
    $tempAddrStmt->execute();
    $tempAddrStmt->close();
}


// Get current approval and check-in status from DB
$getStatus = $conn->prepare("SELECT is_approved, is_checked_in FROM students WHERE id = ?");
$getStatus->bind_param("i", $student_id);
$getStatus->execute();
$getStatus->bind_result($current_is_approved, $current_is_checked_in);
$getStatus->fetch();
$getStatus->close();

// Determine if email should be sent based on changes
$shouldSendApprovalEmail = ($current_is_approved == 0 && $is_approved == 1);
$shouldSendCheckinEmail = ($current_is_checked_in == 0 && $is_checked_in == 1);



// Update student
$updateSql = "UPDATE students SET 
    first_name=?, last_name=?, email=?, varsity_id=?, gender=?, contact_number=?, emergency_contact=?, 
    father_name=?, father_contact=?, mother_name=?, mother_contact=?, hostel_id=?, floor_id=?, room_id=?, 
    is_checked_in=?, check_in_at=?, check_out_at=?, is_verified=?, is_approved=?, verification_token=NULL
    WHERE id=?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param(
    "ssssssssssiiiisssiii",
    $first_name, $last_name, $email, $varsity_id, $gender, $contact_number, $emergency_contact,
    $father_name, $father_contact, $mother_name, $mother_contact, $hostel_id, $floor_id, $room_id,
    $is_checked_in, $check_in_at, $check_out_at, $is_verified, $is_approved, $student_id
);


if ($stmt->execute()) {
    $emailMessage = '';


    if ($shouldSendApprovalEmail) {
        sendStudentApprovalNotification($student_id, $hostel_id, $floor_id, $room_id);
        $emailMessage .= '<br/> Approval email sent to the student.';
    }

    if ($shouldSendCheckinEmail) {
        sendStudentCheckInNotification($student_id, $hostel_id, $floor_id, $room_id);
        $emailMessage .= '<br/> Check-in confirmation email sent to the student.';
    }


    echo json_encode([
        'success' => true,
        'message' => 'Student updated successfully.' . $emailMessage
    ]);
}



$stmt->close();