<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_student();

header('Content-Type: application/json');

$hostel_id = isset($_GET['hostel_id']) ? (int)$_GET['hostel_id'] : 0;

if (!$hostel_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, floor_number, floor_name FROM floors WHERE hostel_id = ? ORDER BY floor_number ASC");
$stmt->bind_param("i", $hostel_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result);
