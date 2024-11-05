<?php
include 'db.php';

// Fetch all members with assigned plans
$sql = "SELECT Member.Name, Plan.PlanName, Membership.StartDate, Membership.EndDate, Membership.Status
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
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
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
                echo "<tr><td>" . htmlspecialchars($row["Name"]) . "</td><td>" . htmlspecialchars($row["PlanName"]) . "</td><td>" 
                . htmlspecialchars($row["StartDate"]) . "</td><td>" . htmlspecialchars($row["EndDate"]) . "</td><td>" 
                . htmlspecialchars($row["Status"]) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No plans assigned</td></tr>";
        }
        ?>

    </table>

    <h2>Available Fitness Plans</h2>
    <table>
        <tr>
            <th>Plan Name</th>
            <th>Rate</th>
        </tr>
        <tr>
            <td>Per Session No Cardio</td>
            <td>70</td>
        </tr>
        <tr>
            <td>Per Session with Cardio Equipment</td>
            <td>100</td>
        </tr>
        <tr>
            <td>15 Days</td>
            <td>500</td>
        </tr>
        <tr>
            <td>30 Days</td>
            <td>900</td>
        </tr>
    </table>
</body>
</html>

<?php
$conn->close();
?>
