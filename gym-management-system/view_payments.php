<?php
session_start(); // Start the session to retrieve session variables

include 'db.php'; // Include the database connection file

// Fetch all payment methods
$paymentMethods = [];
$payment_methods_result = $conn->query("SELECT PaymentMethodID, MethodName FROM PaymentMethods");
if ($payment_methods_result) {
    while ($method = $payment_methods_result->fetch_assoc()) {
        $paymentMethods[$method['PaymentMethodID']] = $method['MethodName'];
    }
}

// Fetch all payments with member names, chosen plans, payment methods
$sql = "SELECT 
            Member.Name AS MemberName, 
            Payment.PaymentDate, 
            Payment.Amount, 
            Payment.ChosenPlan, 
            Payment.PaymentMethodID
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

    .search-bar {
        margin-bottom: 20px;
    }

    .search-bar input {
        padding: 8px;
        font-size: 16px;
        width: 300px;
    }

    /* Checkbox labels styled to be white */
    label {
        color: white;  /* Makes the label text white */
        display: block; /* Ensures labels are block-level for alignment */
        margin: 5px 0; /* Optional: Add some spacing between checkboxes */
    }
    #generateReportBtn {
        display: block;
        margin: 20px 0;
        padding: 10px 20px;
        font-size: 16px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;

    }

    #generateReportBtn:hover {
        background-color: #45a049;
    }
    </style>
</head>
<body>

<h2>View Payments</h2>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search by Name..." onkeyup="filterPayments()">
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

<!-- Generate Report Button -->
<button id="generateReportBtn" onclick="generateReport()">Generate Report</button>

<!-- Report Modal -->
<div id="reportModal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); z-index: 1000;">
    <h3>Total Sales Report</h3>
    <div id="reportContent"></div>
    <button onclick="closeModal()">Close</button>
</div>

<!-- Modal Background -->
<div id="modalBackground" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999;" onclick="closeModal()"></div>

<!-- Payments Table -->
<table id="paymentsTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Plan Name</th>
            <th>Payment Method</th>
            <th>Amount</th>
            <th>Payment Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Fetch plan name based on ChosenPlan
                $chosenPlan = htmlspecialchars($row["ChosenPlan"]) ?: "Unknown Plan";
                // Get payment method name
                $paymentMethod = htmlspecialchars($paymentMethods[$row["PaymentMethodID"]] ?? "Unknown Method");

                echo "<tr>
                        <td>" . htmlspecialchars($row["MemberName"]) . "</td>
                        <td>" . $chosenPlan . "</td>
                        <td>" . $paymentMethod . "</td>
                        <td>₱" . number_format($row["Amount"], 2) . "</td>
                        <td>" . htmlspecialchars($row["PaymentDate"]) . "</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='no-payments'>No payments found</td></tr>";
        }
        ?>
    </tbody>
</table>

<script>
    // Search function
    function filterPayments() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let table = document.getElementById("paymentsTable");
        let rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) { // Skip the header row
            let cells = rows[i].getElementsByTagName("td");
            let name = cells[0]?.textContent.toLowerCase();
            let date = cells[4]?.textContent.toLowerCase();

            if (name.includes(input) || date.includes(input)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }

        // Handle date filters
        const thisMonthCheckbox = document.getElementById("thisMonthCheckbox");
        const sixMonthsCheckbox = document.getElementById("sixMonthsCheckbox");
        const thisYearCheckbox = document.getElementById("thisYearCheckbox");
        const today = new Date();

        for (let i = 1; i < rows.length; i++) {
            const dateCell = rows[i].querySelector("td:nth-child(5)");
            if (dateCell) {
                const paymentDate = new Date(dateCell.textContent.trim());
                const yearDifference = today.getFullYear() - paymentDate.getFullYear();
                const monthDifference = today.getMonth() - paymentDate.getMonth() + yearDifference * 12;

                let showRow = false;

                if (
                    thisMonthCheckbox.checked &&
                    today.getFullYear() === paymentDate.getFullYear() &&
                    today.getMonth() === paymentDate.getMonth()
                ) {
                    showRow = true;
                } else if (sixMonthsCheckbox.checked && monthDifference <= 6) {
                    showRow = true;
                } else if (thisYearCheckbox.checked && yearDifference === 0) {
                    showRow = true;
                }

                rows[i].style.display = showRow ? "" : "none";
            }
        }
    }
</script>

</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
