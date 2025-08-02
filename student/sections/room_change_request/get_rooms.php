<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_student();

header('Content-Type: application/json');

$floor_id = isset($_GET['floor_id']) ? (int)$_GET['floor_id'] : 0;

if (!$floor_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, room_number FROM rooms WHERE floor_id = ? ORDER BY room_number ASC");
$stmt->bind_param("i", $floor_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode($result);
