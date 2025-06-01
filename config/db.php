<?php

require_once __DIR__ . '/config.php';  // just include once

// Now BASE_PATH and env variables are loaded, no need to repeat

// Database connection here or include from db.php
$conn = new mysqli(
    $_ENV['DB_HOST'] ?? 'localhost', 
    $_ENV['DB_USER'] ?? 'root', 
    $_ENV['DB_PASS'] ?? '', 
    $_ENV['DB_NAME'] ?? 'hostel_management'
);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
