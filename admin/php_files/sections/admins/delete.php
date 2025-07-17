<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

header('Content-Type: application/json');

// Check if request is POST and has 'id'
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
    exit;
}

$adminId = (int) $_POST['id'];

// Prevent deleting current logged-in admin
if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $adminId) {
    echo json_encode([
        'success' => false,
        'message' => "You cannot delete your own account."
    ]);
    exit;
}

// Check if admin exists
$stmt = $conn->prepare("SELECT id FROM admins WHERE id = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => "Admin not found."
    ]);
    exit;
}
$stmt->close();

// Delete admin
$deleteStmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
$deleteStmt->bind_param("i", $adminId);

if ($deleteStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => "Admin deleted successfully."
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => "Failed to delete admin."
    ]);
}

$deleteStmt->close();
$conn->close();
exit;
