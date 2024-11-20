<?php
session_start();
include 'db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and escape it to prevent SQL injection
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $province = $conn->real_escape_string($_POST['province']);
    $zipcode = $conn->real_escape_string($_POST['zipcode']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $date_of_birth = $conn->real_escape_string($_POST['date_of_birth']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $physical_condition = $conn->real_escape_string($_POST['physical_condition']);
    $plan_id = $conn->real_escape_string($_POST['plan_id']);
    $height = $conn->real_escape_string($_POST['height']);
    $weight = $conn->real_escape_string($_POST['weight']);

    // Insert into the database (you might have to adjust this query)
    $sql = "INSERT INTO member (Name, Address, City, Province, Zipcode, Gender, DateOfBirth, PhoneNo, EmailID, PhysicalCondition)
            VALUES ('$name', '$address', '$city', '$province', '$zipcode', '$gender', '$date_of_birth', '$phone', '$email', '$physical_condition')";

    if ($conn->query($sql) === TRUE) {
        // Set the success message in session
        $_SESSION['success_message'] = "Successfully Registered!";
        
        // Redirect back to the index page
        header("Location: index.php"); // Redirect to index.php
        exit; // Exit to prevent further execution
    } else {
        // Handle error
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>
