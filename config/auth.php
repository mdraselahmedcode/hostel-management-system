<?php
session_start();  // Ensure session is available

require_once __DIR__ . '/../config/config.php'; // Include for BASE_URL

function is_student_logged_in() {
    return isset($_SESSION['student']) && $_SESSION['student']['logged_in'] === true;
}

function is_admin_logged_in() {
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']['id']);
}

function get_admin_type() {
    return $_SESSION['admin']['admin_type'] ?? null;
}

function require_student($redirectTo = '/student/login.php') {
    if (!is_student_logged_in()) {
        header("Location: " . BASE_URL . $redirectTo);
        exit;
    }
}

function require_admin($redirectTo = '/admin/login.php') {
    if (!is_admin_logged_in()) {
        header("Location: " . BASE_URL . $redirectTo);
        exit;
    }
}

function require_student_or_admin($redirectTo = '/login.php') {
    if (!is_student_logged_in() && !is_admin_logged_in()) {
        header("Location: " . BASE_URL . $redirectTo);
        exit;
    }
}


function require_admin_type($expectedType, $redirectTo = '/unauthorized.php') {
    if (!is_admin_logged_in() || get_admin_type() !== $expectedType) {
        header("Location: " . BASE_URL . $redirectTo);
        exit;
    }
}

// Optional: return JSON if unauthorized (for APIs/AJAX)
function require_api_student() {
    if (!is_student_logged_in()) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized: Student login required"
        ]);
        exit;
    }
}

function require_api_admin() {
    if (!is_admin_logged_in()) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized: Admin login required"
        ]);
        exit;
    }
}
