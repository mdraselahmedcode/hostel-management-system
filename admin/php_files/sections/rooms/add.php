<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$room_number  = strtolower(trim($_POST['room_number'] ?? ''));
$max_capacity = (int) ($_POST['max_capacity'] ?? 0);
$room_type_id = (int) ($_POST['room_type_id'] ?? 0);
$floor_id     = (int) ($_POST['floor_id'] ?? 0);
$hostel_id    = (int) ($_POST['hostel_id'] ?? 0);

// Basic validation
if (!$room_number || !$max_capacity || !$room_type_id || !$floor_id || !$hostel_id) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Get room type details
$roomTypeStmt = $conn->prepare("
    SELECT default_capacity, buffer_limit, type_name
    FROM room_types
    WHERE id = ? AND hostel_id = ?
");
$roomTypeStmt->bind_param("ii", $room_type_id, $hostel_id);
$roomTypeStmt->execute();
$roomTypeResult = $roomTypeStmt->get_result();
$roomTypeStmt->close();

if ($roomTypeResult->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid room type selected.']);
    exit;
}

$roomType = $roomTypeResult->fetch_assoc();
$default_capacity = (int) $roomType['default_capacity'];
$buffer_limit     = (int) $roomType['buffer_limit'];
$typeName         = !empty($roomType['type_name']) ? $roomType['type_name'] : 'this';

// Validation rules
if ($max_capacity < $default_capacity) {
    echo json_encode([
        'success' => false,
        'message' => "Max capacity cannot be less than the default capacity ({$default_capacity}) for {$typeName} rooms."
    ]);
    exit;
}


if ($buffer_limit <= 0 && $max_capacity > $default_capacity) {
    echo json_encode([
        'success' => false,
        'message' => "You need to define how much the max capacity can exceed the default capacity for {$typeName} type rooms."
    ]);
    exit;
}

if ($buffer_limit > 0 && $max_capacity > $default_capacity + $buffer_limit) {
    echo json_encode([
        'success' => false,
        'message' => "Max capacity cannot exceed default capacity ({$default_capacity}) by more than {$buffer_limit} for {$typeName} rooms."
    ]);
    exit;
}

// Check if a room with the same number already exists in the same hostel
$duplicateStmt = $conn->prepare("
    SELECT id FROM rooms
    WHERE hostel_id = ? AND LOWER(room_number) = ?
    LIMIT 1
");
$duplicateStmt->bind_param("is", $hostel_id, $room_number); // $room_number is already lowercased
$duplicateStmt->execute(); 
$duplicateResult = $duplicateStmt->get_result(); 
$duplicateStmt->close(); 

if($duplicateResult->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'A room with this number already exists in the selected hostel.'
    ]);
    exit; 
}


// Insert into DB
$stmt = $conn->prepare("
    INSERT INTO rooms (room_number, max_capacity, room_type_id, floor_id, hostel_id)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param('siiii', $room_number, $max_capacity, $room_type_id, $floor_id, $hostel_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add room.']);
}

$stmt->close();
$conn->close();
