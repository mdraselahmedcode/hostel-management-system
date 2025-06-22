<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
    exit;
}

$student_id = intval($_POST['id']);

// Fetch address IDs
$addressStmt = $conn->prepare('SELECT permanent_address_id, temporary_address_id FROM students WHERE id = ?');
$addressStmt->bind_param('i', $student_id);
$addressStmt->execute();
$addressStmt->store_result();

if ($addressStmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Student not found.']);
    exit;
}

$addressStmt->bind_result($permanent_address_id, $temporary_address_id);
$addressStmt->fetch();
$addressStmt->close();

// Delete student
$delete = $conn->prepare('DELETE FROM students WHERE id = ?');
$delete->bind_param('i', $student_id);
$deleteSuccess = $delete->execute();
$delete->close();

if ($deleteSuccess) {
    // Delete addresses if they exist
    if ($permanent_address_id) {
        $delPerm = $conn->prepare('DELETE FROM addresses WHERE id = ?');
        $delPerm->bind_param('i', $permanent_address_id);
        $delPerm->execute();
        $delPerm->close();
    }
    if ($temporary_address_id) {
        $delTemp = $conn->prepare('DELETE FROM addresses WHERE id = ?');
        $delTemp->bind_param('i', $temporary_address_id);
        $delTemp->execute();
        $delTemp->close();
    }
    echo json_encode(['success' => true, 'message' => 'Student and addresses deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete student.']);
}

$conn->close();