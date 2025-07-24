<?php
require_once __DIR__ . '/../../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Something went wrong'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $account_number = trim($_POST['account_number'] ?? null);
    $active = isset($_POST['active']) ? 1 : 0;

    if ($name === '') {
        $errors[] = "Name is required";
    }
    if ($display_name === '') {
        $errors[] = "Display name is required";
    }

    if (!empty($errors)) {
        $response['message'] = implode('<br>', $errors);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO payment_methods (name, display_name, account_number, active, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("sssi", $name, $display_name, $account_number, $active);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Payment method added successfully";
        } else {
            $response['message'] = "Failed to add payment method";
        }
    }
}

echo json_encode($response);
exit();
