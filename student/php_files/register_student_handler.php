<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';
require_once BASE_PATH . '/admin/includes/response_helper.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate required fields according to schema
    $requiredFields = [
        'first_name', 'last_name', 'email', 'password', 'confirm_password',
        'varsity_id', 'gender', 'contact_number'
    ];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Please fill in all required fields");
        }
    }

    // Validate email format
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    // Validate password match
    if ($_POST['password'] !== $_POST['confirm_password']) {
        throw new Exception("Passwords do not match");
    }

    // Validate phone number format (basic international format)
    if (!preg_match('/^\+?[0-9]{8,15}$/', $_POST['contact_number'])) {
        throw new Exception("Invalid phone number format");
    }

    // Prepare values for checking uniqueness
    $email = $conn->real_escape_string($_POST['email']);
    $varsityId = $conn->real_escape_string($_POST['varsity_id']);
    $contactNumber = $conn->real_escape_string($_POST['contact_number']);

    // Check for existing records using prepared statements
    $checkQuery = $conn->prepare("SELECT 
        SUM(email = ?) as email_exists,
        SUM(varsity_id = ?) as varsity_id_exists,
        SUM(contact_number = ?) as contact_number_exists
        FROM students");

    $checkQuery->bind_param("sss", $email, $varsityId, $contactNumber);
    $checkQuery->execute();
    $result = $checkQuery->get_result()->fetch_assoc();

    if ($result['email_exists'] > 0) {
        throw new Exception("Email already registered");
    }

    if ($result['varsity_id_exists'] > 0) {
        throw new Exception("Student ID already registered");
    }

    if ($result['contact_number_exists'] > 0) {
        throw new Exception("Phone number already registered");
    }

    // Hash password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Handle optional fields
        $emergency_contact = $_POST['emergency_contact'] ?? '';
        $father_name = $_POST['father_name'] ?? '';
        $father_contact = $_POST['father_contact'] ?? '';
        $mother_name = $_POST['mother_name'] ?? '';
        $mother_contact = $_POST['mother_contact'] ?? '';
        $detail = $_POST['detail'] ?? '';

        // Validate and set blood group
        $allowed_blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'];
        $blood_group = in_array($_POST['blood_group'] ?? '', $allowed_blood_groups) 
            ? $_POST['blood_group'] 
            : 'Unknown';

        // Insert student basic info
        $stmt = $conn->prepare("INSERT INTO students (
            first_name, last_name, email, password, varsity_id, department, batch_year, 
            blood_group, gender, contact_number, emergency_contact, father_name, 
            father_contact, mother_name, mother_contact, detail
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssssssssssssssss",
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $password,
            $_POST['varsity_id'],
            $_POST['department'],
            $_POST['batch_year'],
            $blood_group,
            $_POST['gender'],
            $_POST['contact_number'],
            $emergency_contact,
            $father_name,
            $father_contact,
            $mother_name,
            $mother_contact,
            $detail
        );

        if (!$stmt->execute()) {
            throw new Exception("Error saving student information: " . $stmt->error);
        }

        $studentId = $conn->insert_id;
        $permAddressId = null;
        $tempAddressId = null;

        // Insert permanent address if provided
        if (!empty($_POST['perm_country_id'])) {
            // Prepare all address fields
            $perm_state = $_POST['perm_state'] ?? '';
            $perm_division = $_POST['perm_division'] ?? '';
            $perm_district = $_POST['perm_district'] ?? '';
            $perm_sub_district = $_POST['perm_sub_district'] ?? '';
            $perm_village = $_POST['perm_village'] ?? '';
            $perm_postalcode = $_POST['perm_postalcode'] ?? '';
            $perm_street = $_POST['perm_street'] ?? '';
            $perm_house_no = $_POST['perm_house_no'] ?? '';
            $perm_detail = $_POST['perm_detail'] ?? '';

            $stmt = $conn->prepare("INSERT INTO addresses (
                country_id, state, division, district,
                sub_district, village, postalcode, street, house_no, detail
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "isssssssss",
                $_POST['perm_country_id'],
                $perm_state,
                $perm_division,
                $perm_district,
                $perm_sub_district,
                $perm_village,
                $perm_postalcode,
                $perm_street,
                $perm_house_no,
                $perm_detail
            );

            if (!$stmt->execute()) {
                throw new Exception("Error saving permanent address: " . $stmt->error);
            }
            $permAddressId = $conn->insert_id;
        }

        // Insert temporary address if provided and different from permanent
        if (!empty($_POST['temp_country_id']) && empty($_POST['same-address'])) {
            // Prepare all address fields
            $temp_state = $_POST['temp_state'] ?? '';
            $temp_division = $_POST['temp_division'] ?? '';
            $temp_district = $_POST['temp_district'] ?? '';
            $temp_sub_district = $_POST['temp_sub_district'] ?? '';
            $temp_village = $_POST['temp_village'] ?? '';
            $temp_postalcode = $_POST['temp_postalcode'] ?? '';
            $temp_street = $_POST['temp_street'] ?? '';
            $temp_house_no = $_POST['temp_house_no'] ?? '';
            $temp_detail = $_POST['temp_detail'] ?? '';

            $stmt = $conn->prepare("INSERT INTO addresses (
                country_id, state, division, district,
                sub_district, village, postalcode, street, house_no, detail
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "isssssssss",
                $_POST['temp_country_id'],
                $temp_state,
                $temp_division,
                $temp_district,
                $temp_sub_district,
                $temp_village,
                $temp_postalcode,
                $temp_street,
                $temp_house_no,
                $temp_detail
            );

            if (!$stmt->execute()) {
                throw new Exception("Error saving temporary address: " . $stmt->error);
            }
            $tempAddressId = $conn->insert_id;
        }

        // Update student with address IDs if they exist
        if ($permAddressId || $tempAddressId) {
            $stmt = $conn->prepare("UPDATE students SET 
                permanent_address_id = ?,
                temporary_address_id = ?
                WHERE id = ?");
            
            $permAddressId = $permAddressId ?: null;
            $tempAddressId = $tempAddressId ?: null;
            
            $stmt->bind_param(
                "iii",
                $permAddressId,
                $tempAddressId,
                $studentId
            );

            if (!$stmt->execute()) {
                throw new Exception("Error updating student address references: " . $stmt->error);
            }
        }

        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Student registered successfully!";
        $response['student_id'] = $studentId;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>