<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

header('Content-Type: application/json');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Student deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID.']);
}
