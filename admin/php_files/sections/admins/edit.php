<?php
session_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Check admin ID in POST
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => ['Invalid admin ID']]);
    exit;
}

$adminId = (int) $_POST['id'];
$errors = [];

// Receive POST data
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$admin_type_id = isset($_POST['admin_type_id']) ? (int)$_POST['admin_type_id'] : 0;

// Validation
if (empty($firstname)) $errors[] = "First name is required.";
if (empty($lastname)) $errors[] = "Last name is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
if ($admin_type_id <= 0) $errors[] = "Please select a valid admin type.";

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Update admin in DB
$stmt = $conn->prepare("UPDATE admins SET firstname = ?, lastname = ?, email = ?, admin_type_id = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param('sssii', $firstname, $lastname, $email, $admin_type_id, $adminId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Admin updated successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ['Failed to update admin.']]);
}
$stmt->close();
?>
