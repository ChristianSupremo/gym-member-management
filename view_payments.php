<?php
include 'db.php';

// Fetch all payments
$sql = "SELECT Member.Name, Payment.Amount, Payment.PaymentDate, Payment.Status
        FROM Payment
        JOIN Membership ON Payment.MembershipID = Membership.MembershipID
        JOIN Member ON Membership.MemberID = Member.MemberID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>
</head>
<body>
    <h2>Payment History</h2>
    <table border="1">
        <tr>
            <th>Member Name</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Status</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["Name"] . "</td><td>" . $row["Amount"] . "</td><td>" 
                . $row["PaymentDate"] . "</td><td>" . $row["Status"] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No payments found</td></tr>";
        }
        ?>

    </table>
</body>
</html>

<?php
$conn->close();
?>
