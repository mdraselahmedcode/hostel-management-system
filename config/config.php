<?php

// // config/config.php

// require_once __DIR__ . '/../vendor/autoload.php';

// // Load environment variables once
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
// $dotenv->load();

// // Define filesystem absolute path to project root (based on env)
// define('BASE_PATH', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_ENV['BASE_PATH']);

// // Define base URL path for routing/redirects
// define('BASE_URL', rtrim($_ENV['BASE_PATH'], '/') ?: '/hostel-management-system');



require_once __DIR__ . '/../vendor/autoload.php';

// Load .env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// File system path (for includes, file access, etc.)
define('BASE_PATH', rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $_ENV['BASE_PATH']);

// Full base URL (for use in href, email links, etc.)
define('BASE_URL', rtrim($_ENV['BASE_URL'], '/'));

// Payment currency symbol
define('CURRENCY_SYMBOL', $_ENV['CURRENCY_SYMBOL'] ?? '৳');

