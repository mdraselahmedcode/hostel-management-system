<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';

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
        "message" => $errors
    ]);
    exit;
}

// ✅ Fetch admin info with admin type
$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.firstname,
        a.lastname,
        a.email,
        a.password,
        a.admin_type_id,
        at.type_name
    FROM admins a
    LEFT JOIN admin_types at ON a.admin_type_id = at.id
    WHERE a.email = ?
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

$admin = $result->fetch_assoc();

if (!password_verify($password, $admin['password'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Incorrect password."
    ]);
    exit;
}

// ✅ Set session with admin and role info
$_SESSION['admin'] = [
    'id' => $admin['id'],
    'firstname' => $admin['firstname'],
    'lastname' => $admin['lastname'],
    'email' => $admin['email'],
    'admin_type_id' => $admin['admin_type_id'],
    'admin_type_name' => $admin['type_name']
];

// ✅ Prepare successful response
$response = [
    "success" => true,
    "message" => "Login successful.",
    "admin" => [
        'id' => $admin['id'],
        'firstname' => $admin['firstname'],
        'lastname' => $admin['lastname'],
        'email' => $admin['email'],
        'admin_type_id' => $admin['admin_type_id'],
        'admin_type_name' => $admin['type_name']
    ]
];

http_response_code(200);
echo json_encode($response);
exit;

// Clean up
$stmt->close();
$conn->close();




// if ($_SESSION['admin']['admin_type_name'] === 'superadmin') {
//     // Allow full access
// } elseif ($_SESSION['admin']['admin_type_name'] === 'moderator') {
//     // Limited access
// }
