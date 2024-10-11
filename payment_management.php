<?php
include 'db.php';  // Include database connection

// Fetch all members
$members = $conn->query("SELECT MemberID, Name FROM Member");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];

    $sql = "INSERT INTO Payment (MembershipID, Amount, PaymentDate, Status)
            VALUES ('$member_id', '$amount', '$payment_date', 'Completed')";

    if ($conn->query($sql) === TRUE) {
        echo "Payment recorded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
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
    <form method="POST" action="">
        <label for="member_id">Select Member:</label><br>
        <select name="member_id" required>
            <?php
            while($row = $members->fetch_assoc()) {
                echo "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . "</option>";
            }
            ?>
        </select><br>

        <label for="amount">Amount:</label><br>
        <input type="number" name="amount" required><br>

        <label for="payment_date">Payment Date:</label><br>
        <input type="date" name="payment_date" required><br><br>

        <input type="submit" value="Record Payment">
    </form>
</body>
</html>
