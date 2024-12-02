<?php
session_start(); // Start the session to retrieve session variables

include 'db.php'; // Include the database connection file

// Fetch all plans with their rates
$plans = [];
$plans_result = $conn->query("SELECT PlanName, Rate FROM Plan");
if ($plans_result) {
    while ($plan = $plans_result->fetch_assoc()) {
        $plans[number_format($plan['Rate'], 2)] = $plan['PlanName']; // Store rates as keys
    }
}

// Fetch all payments with member names
$sql = "SELECT Payment.PaymentID, Member.Name, Payment.PaymentDate, Payment.Amount
        FROM Payment
        JOIN Membership ON Payment.MembershipID = Membership.MembershipID
        JOIN Member ON Membership.MemberID = Member.MemberID";
$result = $conn->query($sql);

// Check for SQL errors
if (!$result) {
    echo "<p>Error fetching payments: " . htmlspecialchars($conn->error) . "</p>";
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
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
        .no-payments {
            text-align: center;
            color: #888;
        }
        .alert {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert.error {
            background-color: #f44336;
        }
        form {
            display: inline-block;
            margin: 0;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 8px;
            font-size: 16px;
            width: 300px;
        }
    </style>
</head>
<body>

<h2>View Payments</h2>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search by Name or Date..." onkeyup="ftb()">
</div>

<!-- Date Range Checkboxes -->
<label>
    <input type="checkbox" id="thisMonthCheckbox" onchange="filterPayments()"> Payments This Month
</label>
<label>
    <input type="checkbox" id="sixMonthsCheckbox" onchange="filterPayments()"> Payments Within Six Months
</label>
<label>
    <input type="checkbox" id="thisYearCheckbox" onchange="filterPayments()"> Payments This Year
</label>

<!-- Payments Table -->
<table id="paymentsTable">
    <thead>
        <tr>
            <th>Payment ID</th>
            <th>Name</th>
            <th>Payment Date</th>
            <th>Plan Name</th> <!-- Added Plan Name Column -->
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Match the plan name based on the amount
                $planName = isset($plans[number_format($row['Amount'], 2)]) 
                            ? $plans[number_format($row['Amount'], 2)] 
                            : "Unknown Plan";

                echo "<tr>
                        <td>" . htmlspecialchars($row["PaymentID"]) . "</td>
                        <td>" . htmlspecialchars($row["Name"]) . "</td>
                        <td>" . htmlspecialchars($row["PaymentDate"]) . "</td>
                        <td>" . htmlspecialchars($planName) . "</td> <!-- Display Plan Name -->
                        <td>₱" . number_format($row["Amount"], 2) . "</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='no-payments'>No payments found</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>
