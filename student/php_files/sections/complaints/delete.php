<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

header('Content-Type: application/json');
require_student();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student']['id'];
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Check if complaint exists and is pending
    $stmt = $conn->prepare("SELECT id FROM complaints WHERE id = ? AND student_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // Proceed to delete
        $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ? AND student_id = ?");
        $stmt->bind_param("ii", $id, $student_id);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Complaint deleted successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid complaint or not allowed to delete."]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
echo json_encode(["success" => false, "message" => "Invalid request."]);
