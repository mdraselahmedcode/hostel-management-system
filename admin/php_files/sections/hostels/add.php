<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

// 1. CSRF Token Check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}


// Get the logged-in admin ID from session
$admin_id = $_SESSION['admin']['id'];

// Check if this admin is a Super Admin
$admin_check_sql = "SELECT admin_type_id FROM admins WHERE id = ?";
$admin_check_stmt = $conn->prepare($admin_check_sql);
$admin_check_stmt->bind_param('i', $admin_id);
$admin_check_stmt->execute();
$admin_check_result = $admin_check_stmt->get_result();

if ($admin_check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Admin not found.']);
    exit;
}

$admin_data = $admin_check_result->fetch_assoc();
if ((int)$admin_data['admin_type_id'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Permission denied. Only Super Admin can add hostels.']);
    exit;
}





// 2. Validate Required Fields
$requiredFields = [
    'hostel_name', 'hostel_type', 'contact_number', 'capacity',
    'hostel_incharge_id', 'country_id', 'division', 'district',
    'sub_district', 'village', 'postalcode'
];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }
}

// 3. Sanitize Inputs (Hostel)
$hostel_name = $conn->real_escape_string(trim($_POST['hostel_name']));
$hostel_type = $conn->real_escape_string(trim($_POST['hostel_type']));
// $number_of_floors = isset($_POST['number_of_floors']) ? (int) $_POST['number_of_floors'] : 0;
$contact_number = $conn->real_escape_string(trim($_POST['contact_number']));
$capacity = (int) $_POST['capacity'];
$hostel_incharge_id = (int) $_POST['hostel_incharge_id'];
$amenities = $conn->real_escape_string(trim($_POST['amenities'] ?? ''));

// 4. Sanitize Inputs (Address)
$country_id = (int) $_POST['country_id'];
$state = $conn->real_escape_string(trim($_POST['state'] ?? ''));
$division = $conn->real_escape_string(trim($_POST['division']));
$district = $conn->real_escape_string(trim($_POST['district']));
$sub_district = $conn->real_escape_string(trim($_POST['sub_district']));
$village = $conn->real_escape_string(trim($_POST['village']));
$postalcode = $conn->real_escape_string(trim($_POST['postalcode']));
$street = $conn->real_escape_string(trim($_POST['street'] ?? ''));
$house_no = $conn->real_escape_string(trim($_POST['house_no'] ?? ''));
$detail = $conn->real_escape_string(trim($_POST['detail'] ?? ''));

// 5. Insert Address
$address_sql = "INSERT INTO addresses (
    country_id, state, division, district, sub_district, village,
    postalcode, street, house_no, detail
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$address_stmt = $conn->prepare($address_sql);
$address_stmt->bind_param(
    'isssssssss',
    $country_id, $state, $division, $district, $sub_district,
    $village, $postalcode, $street, $house_no, $detail
);

if (!$address_stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert address: ' . $conn->error]);
    exit;
}

$address_id = $address_stmt->insert_id;
$address_stmt->close();

// 6. Insert Hostel
$hostel_sql = "INSERT INTO hostels (
    hostel_incharge_id, address_id, hostel_name,
    hostel_type, contact_number, capacity, amenities
) VALUES (?, ?, ?, ?, ?, ?, ?)";

$hostel_stmt = $conn->prepare($hostel_sql);
$hostel_stmt->bind_param(
    'iisssis',
    $hostel_incharge_id, $address_id, $hostel_name,
    $hostel_type, $contact_number, $capacity, $amenities
);

if ($hostel_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Hostel added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add hostel: ' . $conn->error]);
}

$hostel_stmt->close();
$conn->close();











// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $hostel_name = trim($_POST['hostel_name'] ?? '');
//     $hostel_type = $_POST['hostel_type'] ?? '';
//     $number_of_floors = $_POST['number_of_floors'] ?? null;
//     $contact_number = $_POST['contact_number'] ?? '';
//     $capacity = $_POST['capacity'] ?? 0;
//     $amenities = trim($_POST['amenities'] ?? '');

//     // Validate required fields
//     if (empty($hostel_name) || empty($hostel_type) || empty($contact_number)) {
//         echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
//         exit;
//     }

//     $hostel_incharge_id = $_SESSION['admin']['id'] ?? null;

//     if (!$hostel_incharge_id) {
//         echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
//         exit;
//     }

//     $stmt = $conn->prepare("INSERT INTO hostels (hostel_incharge_id, hostel_name, number_of_floors, hostel_type, contact_number, capacity, amenities) VALUES (?, ?, ?, ?, ?, ?, ?)");
//     $stmt->bind_param("isissis", $hostel_incharge_id, $hostel_name, $number_of_floors, $hostel_type, $contact_number, $capacity, $amenities);

//     if ($stmt->execute()) {
//         echo json_encode(['success' => true, 'message' => 'Hostel added successfully.']);
//     } else {
//         echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
//     }

//     $stmt->close();
//     $conn->close();
// } else {
//     echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
// }
