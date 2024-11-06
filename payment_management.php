<?php
session_start(); // Start the session to store messages

include 'db.php';  // Include your database connection

// Fetch all members from the database
$members = $conn->query("SELECT MemberID, Name FROM Member");

// Fetch all payment methods (assuming you have a PaymentMethods table)
$payment_methods = $conn->query("SELECT PaymentMethodID, MethodName FROM PaymentMethods");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method_id = $_POST['payment_method_id'];  // Ensure this field is included
    $due_date = $_POST['due_date'];  // Ensure this field is included

    // Prepare the SQL query
    $sql = "INSERT INTO Payment (MembershipID, PaymentMethodID, Amount, PaymentDate, DueDate, Status)
            VALUES ('$member_id', '$payment_method_id', '$amount', '$payment_date', '$due_date', 'Completed')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Payment recorded successfully!";  // Store success message
    } else {
        $_SESSION['error_message'] = "Error: " . $conn->error;  // Store error message
    }

    // Redirect to the same page or another page to clear form (optional)
    header("Location: payment_management.php");  // Redirect to the same page after submission
    exit();  // Stop further execution after redirection
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
</head>
<body>
    <h2>Record Payment</h2>

    <!-- Display success or error message if available -->
    <?php
    if (isset($_SESSION['success_message'])) {
        echo "<p style='color: green;'>" . $_SESSION['success_message'] . "</p>";
        unset($_SESSION['success_message']);  // Clear success message
    }

    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']);  // Clear error message
    }
    ?>

    <form method="POST" action="">
        <!-- Member Selection Dropdown -->
        <label for="member_id">Select Member:</label><br>
        <select name="member_id" required>
            <?php
            while($row = $members->fetch_assoc()) {
                echo "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . "</option>";
            }
            ?>
        </select><br>

        <!-- Payment Method Selection Dropdown -->
        <label for="payment_method_id">Select Payment Method:</label><br>
        <select name="payment_method_id" required>
            <?php
            while ($row = $payment_methods->fetch_assoc()) {
                echo "<option value='" . $row['PaymentMethodID'] . "'>" . $row['MethodName'] . "</option>";
            }
            ?>
        </select><br>

        <!-- Payment Amount -->
        <label for="amount">Amount:</label><br>
        <input type="number" name="amount" required><br>

        <!-- Payment Date -->
        <label for="payment_date">Payment Date:</label><br>
        <input type="date" name="payment_date" required><br>

        <!-- Due Date -->
        <label for="due_date">Due Date:</label><br>
        <input type="date" name="due_date" required><br><br>

        <!-- Submit Button -->
        <input type="submit" value="Record Payment">
    </form>
</body>
</html>
