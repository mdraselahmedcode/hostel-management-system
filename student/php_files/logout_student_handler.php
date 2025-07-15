<?php
// filepath: e:\xamp_for_database_donwloading_folder\xampp_installing_folder\htdocs\hostel-management-system\student\php_files\logout_student_handler.php

session_start();
session_unset();
session_destroy();

// Redirect to login page
header('Location: ' . dirname(dirname($_SERVER['REQUEST_URI'])) . '/login_student.php');
exit;