<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/student/php_files/send_email.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, first_name FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No student found with this email.']);
    exit;
}

$student = $result->fetch_assoc();
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

$update = $conn->prepare("UPDATE students SET reset_token = ?, reset_expires = ? WHERE id = ?");
$update->bind_param("ssi", $token, $expires, $student['id']);
$update->execute();

$resetLink = BASE_URL . "/student/sections/reset_password.php?token=$token";
$subject = "Reset Your Hostel Portal Password";
$body = "Hi {$student['first_name']},<br><br>
We received a request to reset your password. Click the link below to proceed:<br>
<a href='$resetLink'>$resetLink</a><br><br>
This link will expire in 1 hour.";

if (sendEmail($email, $subject, $body)) {
    echo json_encode(['success' => true, 'message' => 'Password reset email sent. Please check your inbox.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}
