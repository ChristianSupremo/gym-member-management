<?php
include 'db.php';  // Include the database connection file

// Fetch all members with assigned plans
$sql = "SELECT Member.Name AS MemberName, Plan.PlanName, Membership.StartDate, Membership.EndDate, Membership.Status
        FROM Membership
        JOIN Member ON Membership.MemberID = Member.MemberID
        JOIN Plan ON Membership.PlanID = Plan.PlanID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assigned Plans</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px; /* Adds space between the tables */
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #2F4F4F; /* Changed for better visibility */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #C0C0C0; /* Zebra striping for readability */
        }
        tr:nth-child(odd) {
            background-color: white; /* White background for odd rows */
        }
        tr:hover {
            background-color: #D0E8C5; /* Highlight row on hover */
        }
        .no-data {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>
    <h2>Assigned Fitness Plans</h2>
    <table>
        <tr>
            <th>Member Name</th>
            <th>Plan Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["MemberName"]) . "</td>
                        <td>" . htmlspecialchars($row["PlanName"]) . "</td>
                        <td>" . htmlspecialchars($row["StartDate"]) . "</td>
                        <td>" . htmlspecialchars($row["EndDate"]) . "</td>
                        <td>" . htmlspecialchars($row["Status"]) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='no-data'>No plans assigned</td></tr>";
        }
        ?>
    </table>

    <h2>Available Fitness Plans</h2>
    <table>
        <tr>
            <th>Plan Name</th>
            <th>Rate</th>
        </tr>
        <?php
        // Query to fetch available plans and their rates
        $plan_query = "SELECT PlanName, Rate FROM Plan";
        $plan_result = $conn->query($plan_query);
        
        if ($plan_result->num_rows > 0) {
            while ($plan_row = $plan_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($plan_row["PlanName"]) . "</td>
                        <td>₱" . number_format($plan_row["Rate"], 2) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2' class='no-data'>No available plans</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
