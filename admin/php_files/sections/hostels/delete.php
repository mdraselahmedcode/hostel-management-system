<?php
session_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: " . BASE_URL . "/admin/sections/hostels/index.php");
    exit;
}

$hostelId = intval($_GET['id']);

// Step 1: Get the address_id of the hostel
$getSql = "SELECT address_id FROM hostels WHERE id = ?";
$stmt = $conn->prepare($getSql);
$stmt->bind_param("i", $hostelId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Hostel not found.";
    header("Location: " . BASE_URL . "/admin/sections/hostels/index.php");
    exit;
}

$row = $result->fetch_assoc();
$addressId = $row['address_id'];
$stmt->close();

// Step 2: Delete the hostel
$deleteHostelSql = "DELETE FROM hostels WHERE id = ?";
$stmt = $conn->prepare($deleteHostelSql);
$stmt->bind_param("i", $hostelId);
$hostelDeleted = $stmt->execute();
$stmt->close();

// Step 3: Delete the related address (only if hostel was deleted)
if ($hostelDeleted && $addressId) {
    $deleteAddressSql = "DELETE FROM addresses WHERE id = ?";
    $stmt = $conn->prepare($deleteAddressSql);
    $stmt->bind_param("i", $addressId);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = "Hostel and its address deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete hostel.";
}

$conn->close();
header("Location: " . BASE_URL . "/admin/sections/hostels/index.php");
exit;
