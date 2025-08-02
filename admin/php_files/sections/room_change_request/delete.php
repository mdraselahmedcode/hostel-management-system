<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Check if request exists
    $stmt = $conn->prepare("SELECT status FROM room_change_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();

    if (!$request) {
        echo json_encode(['success' => false, 'message' => 'Request not found.']);
        exit;
    }

    // Optional: prevent deletion of approved requests
    if ($request['status'] === 'approved') {
        echo json_encode(['success' => false, 'message' => 'Approved requests cannot be deleted.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM room_change_requests WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Request deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Deletion failed.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
