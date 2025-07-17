<?php
require_once __DIR__ . '/../../../../config/config.php';  
require_once BASE_PATH . '/config/db.php';  
include BASE_PATH . '/config/auth.php'; 

require_student();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    // Validate and sanitize inputs
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    if (!$student_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
        exit;
    }

    $contact_number = trim($_POST['contact_number'] ?? '');
    if (!$contact_number) {
        echo json_encode(['success' => false, 'message' => 'Contact number is required']);
        exit;
    }

    // Check contact uniqueness excluding this student
    $stmt = $conn->prepare("SELECT id FROM students WHERE contact_number = ? AND id != ?");
    $stmt->bind_param("si", $contact_number, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'This contact number is already used by another student.']);
        exit;
    }

    // Optional fields
    $father_name = trim($_POST['father_name'] ?? '');
    $father_contact = trim($_POST['father_contact'] ?? '');
    $mother_name = trim($_POST['mother_name'] ?? '');
    $mother_contact = trim($_POST['mother_contact'] ?? '');
    $emergency_contact = trim($_POST['emergency_contact'] ?? '');
    $blood_group = $_POST['blood_group'] ?? 'Unknown';

    // Address fields
    $house_no = trim($_POST['house_no'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $village = trim($_POST['village'] ?? '');
    $sub_district = trim($_POST['sub_district'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $division = trim($_POST['division'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $postalcode = trim($_POST['postalcode'] ?? '');

    // Get permanent address ID
    $stmt = $conn->prepare("SELECT permanent_address_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }
    $student = $result->fetch_assoc();
    $permanent_address_id = $student['permanent_address_id'];

    // Update or insert address
    if ($permanent_address_id) {
        $stmt = $conn->prepare("UPDATE addresses SET house_no = ?, street = ?, village = ?, sub_district = ?, district = ?, division = ?, state = ?, postalcode = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $house_no, $street, $village, $sub_district, $district, $division, $state, $postalcode, $permanent_address_id);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to update permanent address']);
            exit;
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO addresses (house_no, street, village, sub_district, district, division, state, postalcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $house_no, $street, $village, $sub_district, $district, $division, $state, $postalcode);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to create permanent address']);
            exit;
        }
        $new_address_id = $stmt->insert_id;

        $stmt = $conn->prepare("UPDATE students SET permanent_address_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_address_id, $student_id);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Failed to link new permanent address']);
            exit;
        }
    }

    // Update student profile
    $stmt = $conn->prepare("UPDATE students SET contact_number = ?, father_name = ?, father_contact = ?, mother_name = ?, mother_contact = ?, emergency_contact = ?, blood_group = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $contact_number, $father_name, $father_contact, $mother_name, $mother_contact, $emergency_contact, $blood_group, $student_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
