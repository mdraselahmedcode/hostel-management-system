<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../../config/config.php';
require_once __DIR__ . '/../../../../config/db.php';
require_once BASE_PATH . '/config/auth.php'; 

require_student(); 

if (!isset($_SESSION['student'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in again.']);
    exit;
}

$student_id = $_SESSION['student']['id'];
$old_password = trim($_POST['old_password'] ?? '');
$new_password = trim($_POST['new_password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if (!$old_password || !$new_password || !$confirm_password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
    exit;
}

// Password strength checks
if (strlen($new_password) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters long.'
    ]);
    exit;
}
if (!preg_match('/[A-Z]/', $new_password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one uppercase letter.'
    ]);
    exit;
}
if (!preg_match('/[a-z]/', $new_password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one lowercase letter.'
    ]);
    exit;
}
if (!preg_match('/[0-9]/', $new_password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one number.'
    ]);
    exit;
}
if (!preg_match('/[\W]/', $new_password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must contain at least one special character.'
    ]);
    exit;
}

// Fetch current password hash
$stmt = $conn->prepare("SELECT password FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($current_hash);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Student not found.']);
    exit;
}
$stmt->close();

// Verify old password
if (!password_verify($old_password, $current_hash)) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
    exit;
}

// Update password
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
$stmt->bind_param("si", $new_hash, $student_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update password. Try again.']);
}
$stmt->close();
$conn->close();
?>