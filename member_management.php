<?php
session_start(); // Start the session to retrieve session variables

include 'db.php';  // Include the database connection file

// Fetch all members with their assigned plans
$sql = "SELECT Member.MemberID, Member.Name, Plan.PlanName, Plan.Rate, Membership.StartDate, Membership.EndDate, Membership.Status
        FROM Membership
        JOIN Member ON Membership.MemberID = Member.MemberID
        JOIN Plan ON Membership.PlanID = Plan.PlanID";
$result = $conn->query($sql);

// Check for SQL errors
if (!$result) {
    echo "<p>Error fetching members: " . htmlspecialchars($conn->error) . "</p>";
    $conn->close();
    exit();
}

// Fetch all available plans with their rates for the dropdown
$plans = $conn->query("SELECT PlanID, PlanName, Rate FROM Plan");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>
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
            background-color: #C0C0C0; /* Zebra striping for readability */
        }
        tr:nth-child(odd) {
            background-color: white; /* White background for odd rows */
        }
        tr:hover {
            background-color: #D0E8C5; /* Highlight row on hover */
        }
        .no-members {
            text-align: center;
            color: #888;
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
        form {
            display: inline-block;
            margin: 0;
        }
    </style>
</head>
<body>

<h2>Member Management</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Current Plan</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["MemberID"]) . "</td>
                        <td>" . htmlspecialchars($row["Name"]) . "</td>
                        <td>" . htmlspecialchars($row["PlanName"]) . " - ₱" . number_format($row["Rate"], 2) . "</td>
                        <td>" . htmlspecialchars($row["StartDate"]) . "</td>
                        <td>" . htmlspecialchars($row["EndDate"]) . "</td>
                        <td>" . htmlspecialchars($row["Status"]) . "</td>
                        <td>
                            <form action='update_plan.php' method='POST'>
                                <input type='hidden' name='member_id' value='" . htmlspecialchars($row["MemberID"]) . "'>
                                <select name='plan_id' required>";
                // Populate dropdown with available plans including rates
                $plans->data_seek(0); // Reset the pointer to the start of $plans
                while ($plan = $plans->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($plan['PlanID']) . "'>" 
                         . htmlspecialchars($plan['PlanName']) . " - ₱" . number_format($plan['Rate'], 2) . "</option>";
                }
                echo "</select>
                            <input type='submit' value='Change Plan'>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='no-members'>No members found</td></tr>";
        }        
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
