<?php
// Database Configuration
$host = getenv("DB_HOST");
$user = getenv("DB_USER");
$password = getenv("DB_PASS");
$db = getenv("DB_NAME");

// Create connection
$conn = new mysqli(
    getenv("DB_HOST"),
    getenv("DB_USER"),
    getenv("DB_PASS"),
    getenv("DB_NAME")
);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
