<?php
include 'db.php'; // Include the database connection file

// Fetch all plans
$sql = "SELECT * FROM Plan"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
            color: #333;
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
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e2e2e2;
        }
        .no-plans {
            text-align: center;
            color: #888;
        }
        form {
            display: inline-block;
            margin: 0;
        }
    </style>
</head>
<body>
    <h2>Plan Management</h2>
    <table>
        <tr>
            <th>Plan ID</th>
            <th>Plan Name</th>
            <th>Rate</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row["PlanID"]) . "</td>
                          <td>" . htmlspecialchars($row["PlanName"]) . "</td>
                          <td>$" . number_format($row["Rate"], 2) . "</td>
                          <td>
                              <form action='edit_plan.php' method='GET'>
                                  <input type='hidden' name='id' value='" . $row["PlanID"] . "'>
                                  <input type='submit' value='Edit'>
                              </form>
                          </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='no-plans'>No plans found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
