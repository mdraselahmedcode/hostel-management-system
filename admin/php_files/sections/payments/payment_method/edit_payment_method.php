<?php
require_once __DIR__ . '/../../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();
header('Content-Type: application/json');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $account_number = trim($_POST['account_number'] ?? null);
    $active = isset($_POST['active']) ? 1 : 0;

    // Validate
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($display_name)) {
        $errors[] = "Display name is required";
    }
    if ($id <= 0) {
        $errors[] = "Invalid ID";
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode('<br>', $errors)
        ]);
        exit;
    }

    // Update
    $stmt = $conn->prepare("
        UPDATE payment_methods 
        SET name = ?, 
            display_name = ?, 
            account_number = ?, 
            active = ?, 
            updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->bind_param("sssii", $name, $display_name, $account_number, $active, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Payment method updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
