<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';


header('Content-Type: application/json');

$response = ['success' => false, 'data' => null];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method", 405);
    }

    // Validate and sanitize inputs
    $email = trim($conn->real_escape_string($_POST['email'] ?? ''));
    $varsityId = trim($conn->real_escape_string($_POST['varsity_id'] ?? ''));

    // At least one identifier required
    if (empty($email) && empty($varsityId)) {
        throw new Exception("Email or Varsity ID required", 400);
    }

    // Validate email format if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format", 400);
    }

    // Validate varsity ID format if provided (alphanumeric with optional hyphens/underscores)
    if (!empty($varsityId) && !preg_match('/^[a-zA-Z0-9\-_]+$/', $varsityId)) {
        throw new Exception("Invalid Varsity ID format", 400);
    }

    // Build query based on provided identifiers
    $query = "SELECT 
                first_name, last_name, email, varsity_id,
                is_verified, is_approved, is_checked_in,
                created_at, updated_at
              FROM students 
              WHERE ";

    $conditions = [];
    $params = [];
    
    if (!empty($email)) {
        $conditions[] = "email = ?";
        $params[] = $email;
    }
    
    if (!empty($varsityId)) {
        $conditions[] = "varsity_id = ?";
        $params[] = $varsityId;
    }

    $query .= implode(" OR ", $conditions);
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("No application found with the provided credentials", 404);
    }

    $studentData = $result->fetch_assoc();
    
    // Verify that both identifiers match the same student if both were provided
    if (!empty($email) && !empty($varsityId)) {
        if ($studentData['email'] !== $email || $studentData['varsity_id'] !== $varsityId) {
            throw new Exception("The provided email and Varsity ID do not match the same student", 400);
        }
    }

    // Only return non-sensitive information
    $responseData = [
        'first_name' => $studentData['first_name'],
        'last_name' => $studentData['last_name'],
        'email' => $studentData['email'],
        'varsity_id' => $studentData['varsity_id'],
        'is_verified' => (bool)$studentData['is_verified'],
        'is_approved' => (bool)$studentData['is_approved'],
        'is_checked_in' => (bool)$studentData['is_checked_in'],
        'created_at' => $studentData['created_at'],
        'updated_at' => $studentData['updated_at']
    ];

    $response['success'] = true;
    $response['data'] = $responseData;

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    $response['message'] = $e->getMessage();
    error_log("Status check error: " . $e->getMessage());
}

// Ensure no sensitive data is leaked
$sanitizedResponse = $response;
unset($sanitizedResponse['data']['password'], $sanitizedResponse['data']['verification_token']);

echo json_encode($sanitizedResponse);
?>