<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

$floorId = intval($_GET['floor_id']);
$roomsQuery = $conn->query("SELECT id, room_number FROM rooms WHERE floor_id = $floorId");
$rooms = $roomsQuery->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($rooms);
$conn->close();
?>
