<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once BASE_PATH . '/student/php_files/send_email.php'; // Make sure path is correct

try {
    // ✅ HARD-CODED TEST DATA
    $studentId = 14; // Use an existing student ID from your DB
    $studentEmail = 'shekhrussel140@gmail.com'; // Use actual email from DB
    $studentFirstName = 'Shekh'; // Optional: For email content

    // 1. Generate token
    $token = bin2hex(random_bytes(32));

    // 2. Save it to database
    $updateTokenStmt = $conn->prepare("UPDATE students SET verification_token = ? WHERE id = ?");
    $updateTokenStmt->bind_param("si", $token, $studentId);
    if (!$updateTokenStmt->execute()) {
        throw new Exception("Failed to store verification token.");
    }

    // 3. Send verification email
    $emailSent = sendVerificationEmail($studentEmail, $studentFirstName, $token);

    if (!$emailSent) {
        throw new Exception("Verification email could not be sent.");
    }

    echo "Verification email sent successfully to $studentEmail.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
