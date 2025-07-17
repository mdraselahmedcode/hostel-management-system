<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Sanitize and validate input
$floorName = trim($_POST['floor_name'] ?? '');
$floorNumber = trim($_POST['floor_number'] ?? '');
$hostelId = trim($_POST['hostel_id'] ?? '');

if (empty($floorName) || empty($floorNumber) || empty($hostelId)) {
    echo json_encode([
        'success' => false,
        'message' => 'All fields are required.'
    ]);
    exit;
}

if (!is_numeric($floorNumber) || !is_numeric($hostelId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Floor number and hostel ID must be numeric.'
    ]);
    exit;
}

// Check if the same floor number already exists for the same hostel
$checkStmt = $conn->prepare("SELECT id FROM floors WHERE floor_number = ? AND hostel_id = ?");
$checkStmt->bind_param("ii", $floorNumber, $hostelId);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'A floor with this number already exists in the selected hostel.'
    ]);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Insert new floor
$stmt = $conn->prepare("INSERT INTO floors (floor_name, floor_number, hostel_id) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $floorName, $floorNumber, $hostelId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Floor added successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add floor. Please try again.'
    ]);
}

$stmt->close();
