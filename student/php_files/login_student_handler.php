<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

include __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../config/db.php';

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$errors = [];

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.';
}

if (empty($password)) {
    $errors[] = 'Password is required.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => implode(" ", $errors)
    ]);
    exit;
}

// Updated query to include is_checked_in check
$stmt = $conn->prepare("
    SELECT id, first_name, last_name, email, password, 
           is_approved, is_verified, is_checked_in 
    FROM students 
    WHERE email = ?
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error. Try again later."
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

$student = $result->fetch_assoc();

if (!password_verify($password, $student['password'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

if (!$student['is_verified']) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Your account is not verified. Please check your email."
    ]);
    exit;
}

if (!$student['is_approved']) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Your account is not approved by the admin yet."
    ]);
    exit;
}

// New check for is_checked_in status
if (!$student['is_checked_in']) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "You cannot login until you have physically checked in at the hostel reception."
    ]);
    exit;
}

// Login successful
$_SESSION['student'] = [
    'id' => $student['id'],
    'first_name' => $student['first_name'],
    'last_name' => $student['last_name'],
    'email' => $email,
    'logged_in' => true
];

echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "redirect" => "dashboard.php"
]);

$stmt->close();
$conn->close();
?>