<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

$errors = [];
$response = ['success' => false];

$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$adminTypeId = (int) ($_POST['admin_type_id'] ?? 0);

// Validation
if (empty($firstname)) $errors[] = "First name is required.";
if (empty($lastname)) $errors[] = "Last name is required.";
if (empty($email)) $errors[] = "Email is required.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
if (empty($password)) $errors[] = "Password is required.";
elseif (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
if ($adminTypeId <= 0) $errors[] = "Please select a valid admin type.";

// Check for existing email
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists.";
    }
    $stmt->close();
}

if (!empty($errors)) {
    $response['errors'] = $errors;
    echo json_encode($response);
    exit;
}

// Insert into DB
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO admins (firstname, lastname, email, password, admin_type_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashedPassword, $adminTypeId);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Admin added successfully.";
} else {
    $response['errors'] = ["Error adding admin. Please try again."];
}

$stmt->close();
echo json_encode($response);
