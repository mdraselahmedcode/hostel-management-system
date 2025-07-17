<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php'; 

require_admin(); 

$hostelId = intval($_GET['hostel_id']);
$floorsQuery = $conn->query("SELECT id, floor_number, floor_name FROM floors WHERE hostel_id = $hostelId");
$floors = $floorsQuery->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($floors);
$conn->close();
?>
