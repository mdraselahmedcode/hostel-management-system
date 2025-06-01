<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

include __DIR__ . '/../../config/config.php';
include __DIR__ . '/../../config/db.php';


$email = trim($_POST['email'] ?? ''); 
$password = trim($_POST['password'] ?? ''); 

$errors = []; 

if(empty($email)) {
    $errors[] = 'Email is required.'; 
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format.'; 
}

if(empty($password)) {
    $errors[] = 'Password is required.'; 
}

if(!empty($errors)) {
    http_response_code(400);  // Bad Request
    echo json_encode([
        "success" => false,
        "message" => $errors
    ]);
    exit; 
}

// Check if user exists
$stmt = $conn->prepare("SELECT id, firstname, lastname, email, password FROM admins WHERE email = ?"); 
if(!$stmt) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "success" => false,
        "message" => "Server error. Try again later." 
    ]);
}
$stmt->bind_param("s", $email); 
$stmt->execute(); 
$result = $stmt->get_result(); 

if($result->num_rows === 0) {
    http_response_code(401);  // Unauthorized (no account)
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password." 
    ]);
    exit; 
}

$admin = $result->fetch_assoc(); 

if(!password_verify($password, $admin['password'])) {
    http_response_code(401);  // Unauthorized (wrong password)
    echo json_encode([
        "success" => false,
        "message" => "Incorrect password."
    ]);
    exit; 
}

$_SESSION['admin'] = [
    'id' => $admin['id'],
    'firstname' => $admin['firstname'],
    'lastname' => $admin['lastname'],
    'email' => $email
]; 

$response = [
    "success" => true,
    "message" => "Login successful.",
    "admin" => [
        'id' => $admin['id'],
        "firstname" => $admin['firstname'],
        "lastname" => $admin['lastname'],
        "email" => $email 
    ]
]; 

http_response_code(200); // OK
echo json_encode($response); 
exit; 


$stmt->close(); 
$conn->close();


?>