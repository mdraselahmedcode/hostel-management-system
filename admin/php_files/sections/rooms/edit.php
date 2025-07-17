<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Sanitize and validate inputs
$room_id       = isset($_POST['room_id']) ? (int) $_POST['room_id'] : 0;
$hostel_id       = isset($_POST['hostel_id']) ? (int) $_POST['hostel_id'] : 0;
$room_number = isset($_POST['room_number']) ? strtolower(trim($_POST['room_number'])) : '';
$max_capacity  = isset($_POST['max_capacity']) ? (int) $_POST['max_capacity'] : 0;
$room_type_id  = isset($_POST['room_type_id']) ? (int) $_POST['room_type_id'] : 0;
$floor_id      = isset($_POST['floor_id']) ? (int) $_POST['floor_id'] : 0;


if ($room_id <= 0 || empty($room_number) || $max_capacity <= 0 || $room_type_id <= 0 || $floor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid.']);
    exit;
}

// Get default capacity for the given room_type_id
$roomTypeStmt = $conn->prepare("
    SELECT default_capacity, buffer_limit, type_name, hostel_id
    FROM room_types 
    WHERE id = ? 
");
$roomTypeStmt->bind_param("i", $room_type_id);
$roomTypeStmt->execute();
$roomTypeResult = $roomTypeStmt->get_result();
$roomTypeStmt->close();

if ($roomTypeResult->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid room type selected.']);
    exit;
}

$roomType = $roomTypeResult->fetch_assoc();
$default_capacity = (int) $roomType['default_capacity'];
$buffer_limit = (int) $roomType['buffer_limit'];

// ✅ Verify that the room type belongs to the same hostel
if ((int)$roomType['hostel_id'] !== (int)$hostel_id) {
    echo json_encode(['success' => false, 'message' => 'The selected room type does not belong to the same hostel.']);
    exit;
}

// Check if the entered max_capacity is less than the default capacity
if ($max_capacity < $default_capacity) {
    echo json_encode([
        'success' => false,
        'message' => "Max capacity cannot be less than the default capacity ($default_capacity) for this room type."
    ]);
    exit;
}

// Safe fallback for type_name
$typeName = isset($roomType['type_name']) && !empty($roomType['type_name'])
    ? $roomType['type_name']
    : 'this';

// If buffer_limit is 0 or less, and max_capacity is greater than default_capacity
if ($buffer_limit <= 0 && $max_capacity > $default_capacity) {
    echo json_encode([
        'success' => false,
        'message' => "You need to define how much the max capacity can exceed the default capacity for {$typeName} type rooms."
    ]);
    exit;
}

// If buffer_limit is set, enforce limit
if ($buffer_limit > 0 && $max_capacity > $default_capacity + $buffer_limit) {
    echo json_encode([
        'success' => false,
        'message' => "Max capacity cannot exceed default capacity ({$default_capacity}) by more than {$buffer_limit} for {$typeName} rooms."
    ]);
    exit;
}


// Check if room exists
$checkStmt = $conn->prepare("
    SELECT id, hostel_id 
    FROM rooms 
    WHERE id = ?
");
$checkStmt->bind_param("i", $room_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$checkStmt->close();


if ($checkResult->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Room not found.']);
    exit;
}

$roomData = $checkResult->fetch_assoc(); 
$hostel_id = $roomData['hostel_id']; 

// Check if a room with the same number already exists in the same hostel
$duplicateStmt = $conn->prepare("
    SELECT id FROM rooms 
    WHERE hostel_id = ? AND LOWER(room_number) = ? AND id != ?
    LIMIT 1 
");
$duplicateStmt->bind_param("isi", $hostel_id, $room_number, $room_id);
$duplicateStmt->execute(); 
$duplicateResult = $duplicateStmt->get_result(); 
$duplicateStmt->close(); 

if($duplicateResult->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Another room with this number already exists in the same hostel.'
    ]);
    exit;
}


// Update room
$updateStmt = $conn->prepare("
    UPDATE rooms
    SET room_number = ?, max_capacity = ?, room_type_id = ?, floor_id = ?
    WHERE id = ?
");
$updateStmt->bind_param("siiii", $room_number, $max_capacity, $room_type_id, $floor_id, $room_id);

if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update room: ' . $conn->error]);
}

$updateStmt->close();
$conn->close();
