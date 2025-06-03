<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['hostel_id']) || !is_numeric($_GET['hostel_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid hostel ID']);
    exit;
}

$hostelId = intval($_GET['hostel_id']);
$stmt = $conn->prepare("SELECT id, type_name FROM room_types WHERE hostel_id = ?");
$stmt->bind_param("i", $hostelId);
$stmt->execute();
$result = $stmt->get_result();

$roomTypes = [];
while ($row = $result->fetch_assoc()) {
    $roomTypes[] = $row;
}

if (!empty($roomTypes)) {
    echo json_encode(['success' => true, 'data' => $roomTypes]);
} else {
    echo json_encode(['success' => false, 'message' => 'No room types found for this hostel']);
}
