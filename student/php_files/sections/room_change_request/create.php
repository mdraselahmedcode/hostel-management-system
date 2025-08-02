<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

header('Content-Type: application/json');
require_student();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student']['id'];

    // Sanitize inputs
    $hostel_id = isset($_POST['hostel_id']) ? (int)$_POST['hostel_id'] : 0;
    $floor_id = isset($_POST['floor_id']) ? (int)$_POST['floor_id'] : 0;
    $room_id = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $details = isset($_POST['details']) ? trim($_POST['details']) : '';

    $reasons = ['roommate issues', 'too noisy', 'prefer different location', 'other'];

    // Validate inputs
    if (!$hostel_id) {
        echo json_encode(['success' => false, 'message' => 'Please select a hostel.']);
        exit;
    }
    if (!$floor_id) {
        echo json_encode(['success' => false, 'message' => 'Please select a floor.']);
        exit;
    }
    if (!$room_id) {
        echo json_encode(['success' => false, 'message' => 'Please select a preferred room.']);
        exit;
    }
    if (!$reason || !in_array($reason, $reasons)) {
        echo json_encode(['success' => false, 'message' => 'Please select a valid reason.']);
        exit;
    }

    // Get student gender
    $stmt = $conn->prepare("SELECT gender FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($student_gender);
    $stmt->fetch();
    $stmt->close();

    if (!$student_gender) {
        echo json_encode(['success' => false, 'message' => 'Student data not found.']);
        exit;
    }

    // Get hostel gender type
    $stmt = $conn->prepare("SELECT hostel_type FROM hostels WHERE id = ?");
    $stmt->bind_param("i", $hostel_id);
    $stmt->execute();
    $stmt->bind_result($hostel_gender);
    $stmt->fetch();
    $stmt->close();

    if (!$hostel_gender) {
        echo json_encode(['success' => false, 'message' => 'Hostel data not found.']);
        exit;
    }

    if ($student_gender !== $hostel_gender) {
        echo json_encode(['success' => false, 'message' => 'You can only request a room change to a hostel matching your gender.']);
        exit;
    }

    // Validate that the selected room belongs to the selected floor and hostel
    $stmt = $conn->prepare("SELECT COUNT(*) FROM rooms WHERE id = ? AND floor_id = ? AND hostel_id = ?");
    $stmt->bind_param("iii", $room_id, $floor_id, $hostel_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        echo json_encode(['success' => false, 'message' => 'Selected room does not belong to the selected floor and hostel.']);
        exit;
    }

    // Insert room change request
    $stmt = $conn->prepare("INSERT INTO room_change_requests (student_id, preferred_room_id, reason, details) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $student_id, $room_id, $reason, $details);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room change request submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit request. Please try again.']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
