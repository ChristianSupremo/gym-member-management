<?php
// Error logging function
function log_error($error_message) {
    $log_file = 'error_log.txt'; // Ensure this file is writable
    $current_date = date("Y-m-d H:i:s");
    file_put_contents($log_file, "[$current_date] $error_message\n", FILE_APPEND);
}

$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";  // Default password is empty for XAMPP
$dbname = "gym_management";  // Make sure this is the correct name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    $error_message = "Database connection failed: " . $conn->connect_error;
    log_error($error_message); // Log the error
    die("Connection failed: " . $conn->connect_error);
}
?>
