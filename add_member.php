<?php
include 'db.php';  // Include the database connection file

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $province = mysqli_real_escape_string($conn, $_POST['province']);
    $zipcode = mysqli_real_escape_string($conn, $_POST['zipcode']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $physical_condition = mysqli_real_escape_string($conn, $_POST['physical_condition']);
    
    // Set Join Date to the current date
    $join_date = date('Y-m-d'); // Current date in 'YYYY-MM-DD' format

    // Insert data into the Member table
    $sql = "INSERT INTO Member (Name, Address, City, Province, Zipcode, Gender, DateOfBirth, PhoneNo, EmailID, PhysicalCondition, JoinDate)
    VALUES ('$name', '$address', '$city', '$province', '$zipcode', '$gender', '$date_of_birth', '$phone', '$email', '$physical_condition', '$join_date')";

    if ($conn->query($sql) === TRUE) {
        echo "New member registered successfully!";
        // Redirect to the dashboard after 3 seconds
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php'; // Redirect to dashboard
                }, 3000); // 3000 milliseconds = 3 seconds
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
} else {
    echo "No form data submitted.";
}
?>
