<?php
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $payment_method_id = $_POST['payment_method_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];

    // Input validation
    if (empty($member_id) || empty($payment_method_id) || empty($amount) || empty($payment_date)) {
        echo "<div class='alert error'>Error: All fields are required.</div>";
        $status = "Failed";
        exit;
    }
    if ($amount <= 0) {
        echo "<div class='alert error'>Error: Amount must be positive.</div>";
        $status = "Failed";
        exit;
    }

    // Validate MembershipID and fetch StartDate
    $membership_check = $conn->query("SELECT m.StartDate, p.Duration 
                                      FROM Membership m
                                      INNER JOIN Plan p ON m.PlanID = p.PlanID
                                      WHERE m.MembershipID = '$member_id'");

    if ($membership_check->num_rows === 0) {
        echo "<div class='alert error'>Error: Invalid Membership ID or Plan.</div>";
        $status = "Failed";
        exit;
    }

    $membership_data = $membership_check->fetch_assoc();
    $start_date = $membership_data['StartDate'];
    $duration_in_days = $membership_data['Duration'];

    // Validate that PaymentDate is not earlier than StartDate
    if (strtotime($payment_date) < strtotime($start_date)) {
        echo "<div class='alert error'>Error: Payment date cannot be earlier than the membership start date.</div>";
        $status = "Failed";
        exit;
    }

    // Calculate Due Date based on the plan duration
    $due_date = date('Y-m-d', strtotime($payment_date . " + $duration_in_days days"));

    // Insert payment with calculated Due Date
    $status = "Completed"; // Default to Completed if all checks pass
    $sql = "INSERT INTO Payment (MembershipID, PaymentMethodID, Amount, PaymentDate, DueDate, Status)
            VALUES ('$member_id', '$payment_method_id', '$amount', '$payment_date', '$due_date', '$status')";

    if ($conn->query($sql) === TRUE) {
        // Update Membership status to 'Active'
        $update_status_sql = "UPDATE Membership SET Status = 'active' WHERE MembershipID = '$member_id'";
        if ($conn->query($update_status_sql) === TRUE) {
            echo "<div class='alert'>Payment recorded successfully! The next due date is: $due_date. Membership status updated to active.</div>";
        } else {
            echo "<div class='alert error'>Error: Unable to update membership status. " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert error'>Error: Unable to record payment. " . $conn->error . "</div>";
        $status = "Failed";
    }

    exit;
}

// Fetch only inactive members with their plans and prices
$members = $conn->query("SELECT m.MembershipID, mem.MemberID, mem.Name AS MemberName, p.PlanName, p.Rate AS PlanPrice
                         FROM Membership m
                         INNER JOIN Member mem ON m.MemberID = mem.MemberID
                         INNER JOIN Plan p ON m.PlanID = p.PlanID
                         WHERE m.Status = 'inactive'");  // Only inactive members

$payment_methods = $conn->query("SELECT PaymentMethodID, MethodName FROM PaymentMethods");
if ($payment_methods->num_rows == 0) {
    echo "<div class='alert error'>No payment methods available. Please add payment methods to the database.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <style>
        /* Existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }

        h2 {
            color: #FDFD96; /* Updated to the requested color */
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #2F4F4F;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #4F8A8B;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: #2F4F4F;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #C0C0C0;
        }

        tr:nth-child(odd) {
            background-color: white;
        }

        tr:hover {
            background-color: #D0E8C5;
        }

        .alert {
            padding: 10px;
            background-color: #4CAF50; /* Green */
            color: white;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert.error {
            background-color: #f44336; /* Red */
        }
    </style>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Correct Select2 CSS -->
     <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Correct Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<body>
    <h2>Record Payment</h2>
    
    <form id="payment-form" method="post">
    <label for="member-select">Select Member:</label>
    <select name="member_id" id="member-select" class="searchable-dropdown" required>
    <?php while ($member = $members->fetch_assoc()) { ?>
        <option value="<?= $member['MembershipID'] ?>">
            <?= $member['MemberID'] ?> - <?= $member['MemberName'] ?> - <?= $member['PlanName'] ?> - ₱<?= number_format($member['PlanPrice'], 2) ?>
        </option>
    <?php } ?>
</select>


        <label for="payment_method_id">Payment Method:</label>
        <select name="payment_method_id" required>
            <?php while ($method = $payment_methods->fetch_assoc()) { ?>
                <option value="<?= $method['PaymentMethodID'] ?>"><?= $method['MethodName'] ?></option>
            <?php } ?>
        </select>

        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" step="0.01" min="0" required />


        <label for="payment_date">Payment Date:</label>
        <input type="date" name="payment_date" id="payment_date" required />


        <button type="submit">Record Payment</button>
    </form>

    <script>
        $(document).ready(function() {
    $('#member-select').select2({
        placeholder: "Search or select a member",
        allowClear: true,
        width: '100%'
    });
});
    </script>

    
</body>
</html>
