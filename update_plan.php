<?php
include 'db.php';  // Include the database connection file

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = mysqli_real_escape_string($conn, $_POST['member_id']);
    $plan_id = mysqli_real_escape_string($conn, $_POST['plan_id']);
    $start_date = date('Y-m-d'); // current date
    $end_date = date('Y-m-d', strtotime('+30 days')); // Example: 30 days from now
    $status = 'Active'; // Change this based on your logic

    // Update Membership table
    $sql = "UPDATE Membership SET PlanID='$plan_id', StartDate='$start_date', EndDate='$end_date', Status='$status' WHERE `Member ID`='$member_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Membership updated successfully!";
        // Redirect back to member management
        header("Location: member_management.php");
        exit();
    } else {
        echo "Error updating membership: " . $conn->error;
    }
}
?>
