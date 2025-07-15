<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/response_helper.php';

// Validate the Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// echo json_encode([
//     'received data' => $_POST,
//     'server method' => $_SERVER['REQUEST_METHOD']
// ]);
// exit; 

// student fields
$first_name = isset($_POST['first_name']) ? htmlspecialchars((trim($_POST['first_name']))) : null;
$last_name = isset($_POST['last_name']) ? htmlspecialchars((trim($_POST['last_name']))) : null;
$email = isset($_POST['email']) ? htmlspecialchars((trim($_POST['email']))) : null;
$password = isset($_POST['password']) ? htmlspecialchars((trim($_POST['password']))) : null;
$confirm_password = isset($_POST['confirm_password']) ? htmlspecialchars((trim($_POST['confirm_password']))) : null; 
$varsity_id = isset($_POST['varsity_id']) ? htmlspecialchars((trim($_POST['varsity_id']))) : null;
$gender = isset($_POST['gender']) ? htmlspecialchars((trim($_POST['gender']))) : null;
$contact_number = isset($_POST['contact_number']) ? htmlspecialchars((trim($_POST['contact_number']))) : null;
$emergency_contact = isset($_POST['emergency_contact']) ? htmlspecialchars((trim($_POST['emergency_contact']))) : null;
$father_name = isset($_POST['father_name']) ? htmlspecialchars((trim($_POST['father_name']))) : null;
$father_contact = isset($_POST['father_contact']) ? htmlspecialchars((trim($_POST['father_contact']))) : null;
$mother_name = isset($_POST['mother_name']) ? htmlspecialchars((trim($_POST['mother_name']))) : null;
$mother_contact = isset($_POST['mother_contact']) ? htmlspecialchars((trim($_POST['mother_contact']))) : null;
$hostel_id = isset($_POST['hostel_id']) ? htmlspecialchars((trim($_POST['hostel_id']))) : null;
$floor_id = isset($_POST['floor_id']) ? htmlspecialchars(trim($_POST['floor_id'])) : null;
$room_id = isset($_POST['room_id']) ? htmlspecialchars(trim($_POST['room_id'])) : null;
$is_checked_in = isset($_POST['is_checked_in']) ? htmlspecialchars(trim($_POST['is_checked_in'])) : null;
$check_in_at = isset($_POST['check_in_at']) ? htmlspecialchars(trim($_POST['check_in_at'])) : null;
$check_out_at = isset($_POST['check_out_at']) ? htmlspecialchars(trim($_POST['check_out_at'])) : null;
$is_verified = isset($_POST['is_verified']) ? htmlspecialchars(trim($_POST['is_verified'])) : null;
$is_approved = isset($_POST['is_approved']) ? htmlspecialchars(trim($_POST['is_approved'])) : null;

// permanent address fields
$perm_country_id = isset($_POST['perm_country_id']) ? htmlspecialchars(trim($_POST['perm_country_id'])) : null;
$perm_state = isset($_POST['perm_state']) ? htmlspecialchars(trim($_POST['perm_state'])) : null;
$perm_division = isset($_POST['perm_division']) ? htmlspecialchars(trim($_POST['perm_division'])) : null;
$perm_district = isset($_POST['perm_district']) ? htmlspecialchars(trim($_POST['perm_district'])) : null;
$perm_sub_district = isset($_POST['perm_sub_district']) ? htmlspecialchars(trim($_POST['perm_sub_district'])) : null;
$perm_village = isset($_POST['perm_village']) ? htmlspecialchars(trim($_POST['perm_village'])) : null;
$perm_postalcode = isset($_POST['perm_postalcode']) ? htmlspecialchars(trim($_POST['perm_postalcode'])) : null;
$perm_street = isset($_POST['perm_street']) ? htmlspecialchars(trim($_POST['perm_street'])) : null;
$perm_house_no = isset($_POST['perm_house_no']) ? htmlspecialchars(trim($_POST['perm_house_no'])) : null;
$perm_detail = isset($_POST['perm_detail']) ? htmlspecialchars(trim($_POST['perm_detail'])) : null;

// temporary address fields
$temp_country_id = isset($_POST['temp_country_id']) ? htmlspecialchars(trim($_POST['temp_country_id'])) : null;
$temp_state = isset($_POST['temp_state']) ? htmlspecialchars(trim($_POST['temp_state'])) : null;
$temp_division = isset($_POST['temp_division']) ? htmlspecialchars(trim($_POST['temp_division'])) : null;
$temp_district = isset($_POST['temp_district']) ? htmlspecialchars(trim($_POST['temp_district'])) : null;
$temp_sub_district = isset($_POST['temp_sub_district']) ? htmlspecialchars(trim($_POST['temp_sub_district'])) : null;
$temp_village = isset($_POST['temp_village']) ? htmlspecialchars(trim($_POST['temp_village'])) : null;
$temp_postalcode = isset($_POST['temp_postalcode']) ? htmlspecialchars(trim($_POST['temp_postalcode'])) : null;
$temp_street = isset($_POST['temp_street']) ? htmlspecialchars(trim($_POST['temp_street'])) : null;
$temp_house_no = isset($_POST['temp_house_no']) ? htmlspecialchars(trim($_POST['temp_house_no'])) : null;
$temp_detail = isset($_POST['temp_detail']) ? htmlspecialchars(trim($_POST['temp_detail'])) : null;



$requiredFields = [
    'first_name',
    'last_name',
    'email',
    'password',
    'confirm_password',
    'varsity_id',
    'gender',
    'contact_number',
    'emergency_contact',
    'father_name',
    'father_contact',
    'mother_name',
    'hostel_id',
    'floor_id',
    'room_id',
    'perm_country_id',
    'perm_division',
    'perm_district',
    'perm_sub_district',
    'perm_village',
    'temp_country_id',
    'temp_division',
    'temp_district',
    'temp_sub_district',
    'temp_village',
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
        'message' => 'Validation error.',
        'missing_fields' => $missingFields
    ]);
    exit;
}

// check_in_at validation
// $date = DateTime::createFromFormat('Y-m-d H:i:s', $_POST['check_in_at']);
// if ($date) {
//     $check_in_at = $date->format('Y-m-d H:i:s');
// } else {
//     echo json_encode([
//         'success' => false,
//         'message' => 'Invalid check-in datetime format'
//     ]); 
// }



// Validate presence
if (empty($password) || empty($confirm_password)) {
    die("Error: Password fields cannot be empty.");
}

if($password !== $confirm_password) {
    echo json_encode([
        'success' => false,
        'message' => 'Passwords do not match.'
    ]);
    exit; 
}

if(strlen($password) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters long.'
    ]);
    exit;
}

if(!preg_match('/[A-Z]/', $password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one uppercase letter.'
    ]); 
    exit;
}

if(!preg_match('/[a-z]/', $password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one lowercase letter.'
    ]); 
    exit; 
}

if(!preg_match('/[0-9]/', $password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one number.'
    ]); 
    exit; 
}

if(!preg_match('/[\W]/', $password)) {
    echo json_encode([
        'success' => false,
        'message' => 'password must contain at least one special character.'
    ]); 
    exit; 
}

// hashing the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT); 




// email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// contact number validation
if (!ctype_digit($contact_number) || strlen($contact_number) < 11) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid contact number.'
    ]);
    exit;
}

// checking country id exists or not
$countryStmt = $conn->prepare("
    SELECT id, country_name
    FROM countries 
    WHERE id = ?
");
$countryStmt->bind_param("i", $perm_country_id);
$countryStmt->execute();
$countryStmt->store_result();
if ($countryStmt->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Permanent country was not found'
    ]);
    exit;
}
$countryStmt->close();

// checking hostel id exists or not
// $hostelStmt = $conn->prepare("
//     SELECT id, hostel_name, hostel_type
//     FROM hostels
//     WHERE id = ? 
// ");
// $hostelStmt->bind_param("i", $hostel_id);
// $hostelStmt->execute();
// $hostelStmt->store_result();
// if ($hostelStmt->num_rows === 0) {
//     echo json_encode([
//         'success' => false,
//         'message' => 'The hostel was not found'
//     ]);
//     exit;
// }

// $hostelStmt->close();

// checking hostel id exists or not
$hostelStmt = $conn->prepare("
    SELECT id, hostel_name, hostel_type
    FROM hostels
    WHERE id = ? 
");
$hostelStmt->bind_param("i", $hostel_id);
$hostelStmt->execute();
$hostelStmt->store_result();
if ($hostelStmt->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'The hostel was not found'
    ]);
    exit;
}

// Checking gender match with hostel type
// Bind the result to variables
$hostelStmt->bind_result($db_hostel_id, $db_hostel_name, $db_hostel_type);
$hostelStmt->fetch();

// Gender/hostel type validation (dynamic, not hardcoded)
$student_gender = strtolower(trim($gender));
$hostel_type = strtolower(trim($db_hostel_type));
// If hostel_type is not 'co-ed' or 'both', check for mismatch
if ($hostel_type !== 'co-ed' && $hostel_type !== 'both' && $hostel_type !== $student_gender) {
    echo json_encode([
        'success' => false,
        'message' => "This hostel is for {$hostel_type} students only."
    ]);
    exit;
}

$hostelStmt->close();

// checking floor id exists in that particular hostel or not
$floorStmt = $conn->prepare("
    SELECT id, floor_name
    FROM floors 
    WHERE id = ? AND hostel_id = ?
");
$floorStmt->bind_param("ii", $floor_id, $hostel_id);
$floorStmt->execute();
$floorStmt->store_result();
if ($floorStmt->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'The floor was not found in the hostel'
    ]);
    exit;
}
$floorStmt->close();

// checking room id exists in that particular floor or not
$roomStmt = $conn->prepare("
    SELECT id, room_number
    FROM rooms 
    WHERE id = ? AND floor_id = ? AND hostel_id = ?
");
$roomStmt->bind_param("iii", $room_id, $floor_id, $hostel_id);
$roomStmt->execute();
$roomStmt->store_result();
if ($roomStmt->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'The room was not found in the floor'
    ]);
    exit;
}
$roomStmt->close();


// checking for existing student by email or varsity_id or contact_number
$checkStudentStmt = $conn->prepare("
    SELECT id 
    FROM students 
    WHERE email = ? OR varsity_id = ? OR contact_number = ?
");
$checkStudentStmt->bind_param("sss", $email, $varsity_id, $contact_number);
$checkStudentStmt->execute();
$checkStudentStmt->store_result();
if ($checkStudentStmt->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'A student with the same email, varsity ID, or contact number already exists.'
    ]);
    exit;
}
$checkStudentStmt->close();


//  Validating whether the room is at it's full capacity or not
$checkRoomAvailableStmt = $conn->prepare("
    SELECT 
    rooms.id AS room_id,
    rooms.room_number,
    COUNT(students.id) AS current_occupancy,
    rooms.max_capacity,
    (rooms.max_capacity - COUNT(students.id)) AS available_space,
    CASE
            WHEN COUNT(students.id) >= rooms.max_capacity THEN 'FULL'
            ELSE 'AVAILABLE'
        END AS status
    FROM rooms
    LEFT JOIN students ON rooms.id = students.room_id
    WHERE rooms.id = ?  
    AND rooms.floor_id = ?
    AND rooms.hostel_id = ?
    GROUP BY rooms.id, rooms.room_number, rooms.max_capacity;
");
// 77 
$checkRoomAvailableStmt->bind_param("iii", $room_id, $floor_id, $hostel_id);
$checkRoomAvailableStmt->execute(); 
$result = $checkRoomAvailableStmt->get_result(); 
if($result->num_rows === 0 ) {
    echo json_encode([
        'success' => false,
        'message' => 'Room not found.'
    ]); 
    exit; 
}

$roomData = $result->fetch_assoc(); 
if($roomData['current_occupancy'] >= $roomData['max_capacity']) {
    echo json_encode([
        'success' => false,
        'message' => 'Room is already at full capacity.'
    ]); 
    exit; 
}

// Start transaction
$conn->begin_transaction();
try {


    // insert premanent and temporary addresses
    $addressStmt = $conn->prepare("
    INSERT INTO addresses (
        country_id, state, division, district, sub_district,
        village, postalcode, street, house_no, detail
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$addressStmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    // Insert Permanent address   ---------------
    $addressStmt->bind_param(
        "isssssssss",
        $perm_country_id,
        $perm_state,
        $perm_division,
        $perm_district,
        $perm_sub_district,
        $perm_village,
        $perm_postalcode,
        $perm_street,
        $perm_house_no,
        $perm_detail
    );
    $permInsertSuccess = $addressStmt->execute();

    if (!$permInsertSuccess) {
        throw new Exception('Failed to insert permanent address: ' . $addressStmt->error);
    }
    $perm_address_id = $conn->insert_id; // Capture the ID for future reference



    // Insert Temporary address   --------------
    $addressStmt->bind_param(
        "isssssssss",
        $temp_country_id,
        $temp_state,
        $temp_division,
        $temp_district,
        $temp_sub_district,
        $temp_village,
        $temp_postalcode,
        $temp_street,
        $temp_house_no,
        $temp_detail
    );
    $tempInsertSuccess = $addressStmt->execute();

    if (!$tempInsertSuccess) {
        throw new Exception('Failed to insert temporary address: ' . $addressStmt->error);
    }
    $temp_address_id = $conn->insert_id; // Capture the ID for future reference
    $addressStmt->close();






    // Insert student
    // creating new student in the system
    $studentStmt = $conn->prepare("
    INSERT INTO students (
            first_name, last_name, email, password, varsity_id, gender, contact_number, emergency_contact, father_name, father_contact, mother_name, mother_contact, hostel_id, floor_id, room_id, is_checked_in, check_in_at, check_out_at, is_verified, is_approved, permanent_address_id, temporary_address_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$studentStmt) {
        throw new Exception('Server error.' . $conn->error);
    }

    $studentStmt->bind_param(
        "ssssssssssssiiisssiiii",
        $first_name,
        $last_name,
        $email,
        $hashedPassword,
        $varsity_id,
        $gender,
        $contact_number,
        $emergency_contact,
        $father_name,
        $father_contact,
        $mother_name,
        $mother_contact,
        $hostel_id,
        $floor_id,
        $room_id,
        $is_checked_in,
        $check_in_at,
        $check_out_at,
        $is_verified,
        $is_approved,
        $perm_address_id,
        $temp_address_id
    );

    $studentInsertSuccess =  $studentStmt->execute();
    if (!$studentInsertSuccess) {
        throw new Exception('Failed to insert student' . $studentStmt->error);
    }
    $studentStmt->close();

    // commit tracsaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Student created successfully.',
        'permanent address id' => $perm_address_id,
        'temporary address id' => $temp_address_id,
        'student id' => $varsity_id
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
$conn->close();
