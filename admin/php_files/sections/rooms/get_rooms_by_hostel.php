<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

header('Content-Type: application/json');

if (!isset($_GET['hostel_id']) || !is_numeric($_GET['hostel_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid hostel ID.',
        'data' => []
    ]);
    exit;
}

$hostelId = intval($_GET['hostel_id']);

$sql = "SELECT id, floor_number FROM floors WHERE hostel_id = ? ORDER BY floor_number ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $hostelId);
$stmt->execute();
$result = $stmt->get_result();

$floors = [];
while ($row = $result->fetch_assoc()) {
    $floors[] = [
        'id' => $row['id'],
        'floor_number' => $row['floor_number']
    ];
}

if (empty($floors)) {
    echo json_encode([
        'success' => false,
        'message' => 'No floors found for this hostel.',
        'data' => []
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Floors fetched successfully.',
        'data' => $floors
    ]);
}
exit;
