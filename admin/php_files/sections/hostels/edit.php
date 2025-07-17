<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Validate CSRF token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Required fields
$requiredFields = ['id', 'hostel_name', 'hostel_type', 'contact_number', 'capacity', 'hostel_incharge_id', 'country_id'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }
}

$hostel_id = intval($_POST['id']);
$hostel_name = trim($_POST['hostel_name']);
$hostel_type = $_POST['hostel_type'];
// $number_of_floors = $_POST['number_of_floors'] ?? null;
$contact_number = trim($_POST['contact_number']);
$capacity = intval($_POST['capacity']);
$hostel_incharge_id = intval($_POST['hostel_incharge_id']);
$amenities = trim($_POST['amenities'] ?? '');

// Address fields
$country_id = intval($_POST['country_id']);
$state = trim($_POST['state'] ?? '');
$division = trim($_POST['division'] ?? '');
$district = trim($_POST['district'] ?? '');
$sub_district = trim($_POST['sub_district'] ?? '');
$village = trim($_POST['village'] ?? '');
$postalcode = trim($_POST['postalcode'] ?? '');
$street = trim($_POST['street'] ?? '');
$house_no = trim($_POST['house_no'] ?? '');
$detail = trim($_POST['detail'] ?? '');

$conn->begin_transaction();

try {
    // Update hostel table
    $stmt = $conn->prepare("UPDATE hostels SET hostel_name = ?, hostel_type = ?, contact_number = ?, capacity = ?, hostel_incharge_id = ?, amenities = ? WHERE id = ?");
    $stmt->bind_param("sssissi", $hostel_name, $hostel_type, $contact_number, $capacity, $hostel_incharge_id, $amenities, $hostel_id);
    $stmt->execute();
    $stmt->close();

    // Fetch address_id from hostels table
    $stmt = $conn->prepare("SELECT address_id FROM hostels WHERE id = ?");
    $stmt->bind_param("i", $hostel_id);
    $stmt->execute();
    $stmt->bind_result($address_id);
    $stmt->fetch();
    $stmt->close();

    if (!$address_id) {
        throw new Exception("Address ID not found.");
    }

    // Update address table
    $stmt = $conn->prepare("UPDATE addresses SET country_id = ?, state = ?, division = ?, district = ?, sub_district = ?, village = ?, postalcode = ?, street = ?, house_no = ?, detail = ? WHERE id = ?");
    $stmt->bind_param("isssssssssi", $country_id, $state, $division, $district, $sub_district, $village, $postalcode, $street, $house_no, $detail, $address_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Hostel updated successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $e->getMessage()]);
}
