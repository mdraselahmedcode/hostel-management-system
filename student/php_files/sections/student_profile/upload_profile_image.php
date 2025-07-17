<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_student();
header('Content-Type: application/json');

// Check if file was uploaded successfully
if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['profile_image'];

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Only JPG, PNG and GIF allowed']);
    exit;
}

$studentId = $_SESSION['student']['id'];

// 1. Fetch current profile image path from DB
$stmt = $conn->prepare("SELECT profile_image_url FROM students WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$currentImageUrl = null;

if ($row = $result->fetch_assoc()) {
    $currentImageUrl = $row['profile_image_url'];
}
$stmt->close();

// 2. Generate unique file name and upload
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFileName = 'profile_' . $studentId . '_' . time() . '.' . $ext;
$uploadDir = BASE_PATH . '/student/assets/images/';
$uploadPath = $uploadDir . $newFileName;

if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
    exit;
}

// Generate new image URL
$newImageUrl = BASE_URL . '/student/assets/images/' . $newFileName;

// 3. Update new image path in DB
$stmt = $conn->prepare("UPDATE students SET profile_image_url = ? WHERE id = ?");
if (!$stmt) {
    unlink($uploadPath); // Delete new image on failure
    echo json_encode(['success' => false, 'error' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param("si", $newImageUrl, $studentId);
if (!$stmt->execute()) {
    unlink($uploadPath);
    echo json_encode(['success' => false, 'error' => 'Database update failed']);
    exit;
}
$stmt->close();

// 4. Delete previous image file if exists and not a default
if ($currentImageUrl) {
    $relativePath = str_replace(BASE_URL, '', $currentImageUrl);
    $previousImagePath = BASE_PATH . $relativePath;

    if (file_exists($previousImagePath)) {
        unlink($previousImagePath);
    }
}

echo json_encode(['success' => true, 'imageUrl' => $newImageUrl]);
