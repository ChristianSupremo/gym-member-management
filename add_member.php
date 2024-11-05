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
        // Get the last inserted MemberID
        $member_id = $conn->insert_id;

        // Set a default plan (if you have a default plan ID)
        $default_plan_id = 1; // Change this as per your logic
        $start_date = date('Y-m-d'); // Use the current date
        $end_date = date('Y-m-d', strtotime('+30 days')); // Example: 30 days from now
        $status = 'Active'; // You may change this based on your needs

        // Insert into Membership table
        $membership_sql = "INSERT INTO Membership (`Member ID`, PlanID, StartDate, EndDate, Status)
        VALUES ('$member_id', '$default_plan_id', '$start_date', '$end_date', '$status')";

        if ($conn->query($membership_sql) === TRUE) {
            echo "New member registered and membership created successfully!";
        } else {
            echo "Error inserting into Membership table: " . $conn->error;
        }

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
