<?php
session_start(); // Start the session to retrieve session variables
include 'db.php'; // Include the database connection file

// Fetch all members with their assigned plans, handling NULL values and calculating validity period
$sql = "SELECT 
            Member.MemberID, 
            Member.Name, 
            Plan.PlanID, 
            Plan.PlanName, 
            Plan.Rate, 
            Membership.StartDate, 
            Membership.EndDate, 
            Membership.PaymentDate, 
            Membership.Status,
            DATEDIFF(Membership.EndDate, CURDATE()) AS ValidityPeriod
        FROM Membership
        JOIN Member ON Membership.MemberID = Member.MemberID
        LEFT JOIN Plan ON Membership.PlanID = Plan.PlanID"; // LEFT JOIN to allow NULL PlanID
$result = $conn->query($sql);

// Fetch all available plans with their rates
$plans_result = $conn->query("SELECT PlanID, PlanName, Rate FROM Plan");

// Count active and inactive members
$activeCount = $conn->query("SELECT COUNT(*) AS count FROM Membership WHERE Status = 'Active'")->fetch_assoc()['count'];
$inactiveCount = $conn->query("SELECT COUNT(*) AS count FROM Membership WHERE Status = 'Inactive'")->fetch_assoc()['count'];

// Check for SQL errors
if (!$result || !$plans_result) {
    echo "<p>Error fetching members or plans: " . htmlspecialchars($conn->error) . "</p>";
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>
    <style>
        /* Styles for the page and modal */
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
        .no-members {
            text-align: center;
            color: #888;
        }
        .pay-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            text-align: center;
        }
        .pay-button:hover {
            background-color: #45a049;
        }

        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.6); /* Dark semi-transparent background */
            transition: all 0.3s ease-in-out;
        }

        /* Modal content box */
        .modal-content {
            background-color: #8a8686;
            margin: 10% auto; /* Centered with 10% margin from the top */
            padding: 30px;
            border-radius: 8px;
            width: 25%; /* Adjust to desired width */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.3s ease-in-out; /* Fade-in animation */
        }

        /* Close button styling */
        .close-button {
            color: #ffffff;
            font-size: 30px;
            font-weight: bold;
            position: absolute;
            top: 200px;
            right: 700px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-button:hover {
            color: #000;
        }

        /* Payment modal heading */
        h2 {
            color: #FDFD96;
            font-size: 18px;
            margin-bottom: 20px;
        }

        /* Label styling for form inputs */
        label {
            display: block;
            margin: 10px 0 5px;
            font-size: 16px;
            color: #ffffff;
        }

        /* Select dropdown and input styling */
        select, input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* Submit button styling */
        .pay-button {
            background-color: #2F4F4F;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        .pay-button:hover {
            background-color: #45a049;
        }

        /* Animation for fade-in effect */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive styles for mobile */
        @media (max-width: 768px) {
            .modal-content {
                width: 80%;
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            .pay-button {
                padding: 12px;
            }
        }

        #searchInput{
                width: 20%;
                margin: 0;
        }
        .active-count {
        color: green;
        }
        .inactive-count {
            color: #c92222;
        }
    </style>
</head>
<body>

<h2>Member Management</h2>

<label for="searchInput">Search Members:</label>
<input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by Name...">

<!-- Member Counts -->
<div style="margin-top: 10px;">
    <span class="active-count"><strong>Active Members:</strong> <?php echo $activeCount; ?></span>
    <span class="inactive-count" style="margin-left: 20px;"><strong>Inactive Members:</strong> <?php echo $inactiveCount; ?></span>
</div>

<!-- Filter Checkboxes -->
<label>
    <input type="checkbox" id="activeCheckbox" onchange="filterByStatus()"> Show Active Members
</label>
<label>
    <input type="checkbox" id="inactiveCheckbox" onchange="filterByStatus()"> Show Inactive Members
</label>


<!-- Members Table -->
<table id="membersTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <!-- Removed the Current Plan column -->
            <th>Start Date</th>
            <th>Valid Until</th> <!-- Display End Date -->
            <th>Validity Period (Days)</th> <!-- Display Days Left -->
            <th>Payment Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["MemberID"]) . "</td>
                        <td>" . htmlspecialchars($row["Name"]) . "</td>";
                // Removed the column displaying the Current Plan
                echo "<td>";
                // Handle Start Date
                echo $row["StartDate"] !== null ? htmlspecialchars($row["StartDate"]) : "Not Started";
                echo "</td>
                        <td>";
                // Handle End Date (Valid Until)
                echo $row["EndDate"] !== null ? htmlspecialchars($row["EndDate"]) : "No Due Date";
                echo "</td>
                        <td>";
                // Handle Validity Period (Days)
                $validityPeriod = $row["ValidityPeriod"];
                if ($validityPeriod > 0) {
                    echo htmlspecialchars($validityPeriod) . " Days Left";
                } else if ($validityPeriod == 0) {
                    echo "Expires Today";
                } else {
                    echo "Expired";
                }
                echo "</td>
                        <td>";
                // Handle Payment Date
                echo $row["PaymentDate"] !== null ? htmlspecialchars($row["PaymentDate"]) : "No Payment Yet";
                echo "</td>
                        <td>" . htmlspecialchars($row["Status"]) . "</td>
                        <td>
                            <button 
                                class='pay-button' 
                                onclick=\"openPaymentModal(" . htmlspecialchars($row['MemberID']) . ", '" . addslashes($row['Name']) . "', " . htmlspecialchars($row['PlanID']) . ")\">
                                Pay
                            </button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='8' class='no-members'>No members found</td></tr>"; // Adjusted colspan to 8
        }
        ?>
    </tbody>
</table>


<!-- Modal for entering payment details -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="closePaymentModal()">&times;</span>
        <h2>Payment for <span id="memberName"></span></h2>
        <form action="payment_management.php" method="POST">
            <input type="hidden" id="modalMemberID" name="member_id">
            <input type="hidden" id="modalPlanID" name="plan_id"> <!-- Hidden Plan ID -->

            <label for="payment_method_id">Payment Method:</label>
            <select name="payment_method_id" required>
                <?php
                // Fetch available payment methods from the database
                $payment_methods = $conn->query("SELECT PaymentMethodID, MethodName FROM PaymentMethods");
                while ($method = $payment_methods->fetch_assoc()) {
                    echo "<option value='" . $method['PaymentMethodID'] . "'>" . $method['MethodName'] . "</option>";
                }
                ?>
            </select>

            <label for="plan_id">Plan:</label>
            <select name="plan_id" id="plan_id" required>
                <?php
                // Fetch all available plans from the database and display
                while ($plan = $plans_result->fetch_assoc()) {
                    echo "<option value='" . $plan['PlanID'] . "'>" . $plan['PlanName'] . " - ₱" . number_format($plan['Rate'], 2) . "</option>";
                }
                ?>
            </select>
            
            <label for="payment_date">Payment Date(Today):</label>
            <input type="text" name="payment_date" value="<?php echo date('Y-m-d'); ?>" readonly>

            <button type="submit" class="pay-button">Submit Payment</button>
        </form>
    </div>
</div>

</body>
</html>

<?php
$conn->close();
?>
