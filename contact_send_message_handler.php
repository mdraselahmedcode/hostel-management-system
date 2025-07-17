<?php
require_once __DIR__ . '/config/config.php';
include BASE_PATH . '/student/php_files/send_email.php';

$response = ['success' => false, 'message' => 'Unknown error'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST["name"] ?? ""));
    $email = htmlspecialchars(trim($_POST["email"] ?? ""));
    $phone = htmlspecialchars(trim($_POST["phone"] ?? ""));
    $subject = htmlspecialchars(trim($_POST["subject"] ?? ""));
    $message = htmlspecialchars(trim($_POST["message"] ?? ""));

    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $response['message'] = "Please fill out all required fields (Name, Email, Subject, Message).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address.";
    } else {
        // Compose HTML email body
        $body = "
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Message:</strong></p>
            <p>$message</p>
        ";

        // Send email
        if (sendEmail("mdraselahmed.code@gmail.com", "Contact Form: $subject", $body)) {
            $response['success'] = true;
            $response['message'] = "✅ Your message has been sent successfully!";
        } else {
            $response['message'] = "❌ There was a problem sending your message. Please try again later.";
        }
    }
}

// Set content type header to JSON
header('Content-Type: application/json');

// Output JSON response
echo json_encode($response);
exit;
?>
