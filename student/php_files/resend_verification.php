<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/student/php_files/send_email.php';

header('Content-Type: application/json');


$email = $_POST['email'] ?? $_GET['email'] ?? '';
$varsity_id = $_POST['varsity_id'] ?? $_GET['varsity_id'] ?? '';

// echo json_encode([$email, $varsity_id]);
// exit;

try {

    if (!$email || !$varsity_id) {
        throw new Exception("Email and Varsity ID are required.");
    }

    // Lookup student by email and varsity ID
    $stmt = $conn->prepare("SELECT id, first_name, is_verified FROM students WHERE email = ? AND varsity_id = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    $stmt->bind_param("ss", $email, $varsity_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No matching student found.");
    }

    $student = $result->fetch_assoc();

    if ($student['is_verified']) {
        throw new Exception("This student is already verified.");
    }

    // Generate new verification token
    $token = bin2hex(random_bytes(32));
    $updateStmt = $conn->prepare("UPDATE students SET verification_token = ? WHERE id = ?");
    if (!$updateStmt) {
        throw new Exception("Database error: " . $conn->error);
    }
    $updateStmt->bind_param("si", $token, $student['id']);
    $updateStmt->execute();

    // Send verification email
    $emailSent = sendVerificationEmail($email, $student['first_name'], $token);
    if (!$emailSent) {
        throw new Exception("Failed to send verification email.");
    }

    echo json_encode([
        'success' => true,
        'message' => "Verification email has been resent to $email"
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
