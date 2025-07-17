<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($token) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Token and new password are required.'
    ]);
    exit;
}

// Look for a student with matching reset token
$stmt = $conn->prepare("SELECT id, reset_expires FROM students WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or expired token.'
    ]);
    exit;
}

$student = $result->fetch_assoc();

// Check if token expired
if (strtotime($student['reset_expires']) < time()) {
    echo json_encode([
        'success' => false,
        'message' => 'The reset token has expired.'
    ]);
    exit;
}

// Hash and update password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$update = $conn->prepare("UPDATE students SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
$update->bind_param("si", $hashedPassword, $student['id']);

if ($update->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Your password has been successfully reset.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update password. Please try again.'
    ]);
}
exit;
?>
