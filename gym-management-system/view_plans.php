<?php
include 'db.php';  // Include the database connection file

session_start(); // Ensure the session is started at the top of the file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add plan
    if (isset($_POST['add_plan'])) {
        $plan_name = $_POST['plan_name'];
        $rate = $_POST['rate'];
        $duration = $_POST['duration']; // Capture duration from form input

        // Insert new plan
        $sql = "INSERT INTO Plan (PlanName, Rate, Duration) VALUES ('$plan_name', '$rate', '$duration')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "Plan added successfully!";
            header("Location: index.php"); // Redirect to the main page
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Edit plan
    if (isset($_POST['edit_plan'])) {
        $plan_id = $_POST['plan_id'];
        $plan_name = $_POST['plan_name'];
        $rate = $_POST['rate'];
        $duration = $_POST['duration']; // Capture duration

        // Update the existing plan
        $sql = "UPDATE Plan SET PlanName = '$plan_name', Rate = '$rate', Duration = '$duration' WHERE PlanID = '$plan_id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "Plan updated successfully!";
            header("Location: index.php"); // Redirect to the main page
            exit;
        } else {
            echo "Error updating plan: " . $conn->error;
        }
    }

    // Delete plan
    if (isset($_POST['delete_plan'])) {
        $plan_id = $_POST['plan_id'];

        // Delete plan from database
        $sql = "DELETE FROM Plan WHERE PlanID = '$plan_id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "Plan deleted successfully!";
            header("Location: index.php"); // Redirect to the main page
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Fetch available plans, sorted by Duration (highest to lowest)
$plan_query = "SELECT PlanID, PlanName, Rate, Duration FROM Plan ORDER BY Duration DESC";
$plan_result = $conn->query($plan_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View and Manage Plans</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"], input[type="number"] {
            /* width: 50%; */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 5px;
            font-size: 16px;
        }

        #plan_name{
            width: 250px;
            margin-left: 30px;
        }
        #rate{
            margin-left: 50px;
        }
        #duration{

        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            margin-left: 115px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2F4F4F;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #e9f9e9;
        }

        .actions form {
            display: inline-block;
            margin: 0 5px;
        }

        .actions input[type="submit"] {
            background-color: #f44336;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 5px;
        }

        .actions input[type="submit"]:hover {
            background-color: #d32f2f;
        }

        .actions input[type="submit"].edit-button {
            background-color: #007BFF; /* Blue color */
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .actions input[type="submit"].edit-button:hover {
            background-color: #0056b3; /* Darker blue for hover effect */
        }

        .no-data {
            text-align: center;
            color: #888;
            padding: 20px;
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

<h2>Plans Management</h2>

<div class="container">
    <!-- Add New Plan Form -->
    <h3>Add New Plan</h3>
    <form action="view_plans.php" method="POST" onsubmit="return ADDvalidatePlan(this)">
        <div class="form-group">
            <label for="plan_name">Plan Name:</label>
            <input type="text" name="plan_name" id="plan_name" required>
        </div>
        <div class="form-group">
            <label for="rate">Rate (₱):</label>
            <input type="number" name="rate" id="rate" required>
        </div>
        <div class="form-group">
            <label for="duration">Duration (days):</label>
            <input type="number" name="duration" id="duration" required>
        </div>
        <input type="submit" name="add_plan" value="Add Plan">
    </form>

    <!-- Search Bar Below Heading -->
    <h3>Available Plans</h3>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search by Plan Name or Duration" onkeyup="searchPlans()">
    </div>

    <!-- Plans Table -->
    <table id="plansTable">
        <tr>
            <th>Plan Name</th>
            <th>Rate</th>
            <th>Duration (Days)</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($plan_result->num_rows > 0) {
            while ($plan_row = $plan_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($plan_row["PlanName"]) . "</td>
                        <td>₱" . number_format($plan_row["Rate"], 2) . "</td>
                        <td>" . htmlspecialchars($plan_row["Duration"]) . "</td>
                        <td class='actions'>
                            <form action='view_plans.php' method='POST' onsubmit='return validatePlan(this);'>
                                <input type='hidden' name='plan_id' value='" . $plan_row['PlanID'] . "'>
                                <input type='text' name='plan_name' value='" . htmlspecialchars($plan_row['PlanName']) . "' required>
                                <input type='number' name='rate' value='" . $plan_row['Rate'] . "' required>
                                <input type='number' name='duration' value='" . $plan_row['Duration'] . "' required>
                                <input type='submit' name='edit_plan' value='Edit' class='edit-button'>
                            </form>
                            <form action='view_plans.php' method='POST' onsubmit='return confirm(\"Are you sure you want to delete this plan?\");'>
                                <input type='hidden' name='plan_id' value='" . $plan_row['PlanID'] . "'>
                                <input type='submit' name='delete_plan' value='Delete'>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='no-data'>No available plans</td></tr>";
        }
        ?>
    </table>
</div>

<script>
    // Search function
    function searchPlans() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let table = document.getElementById("plansTable");
        let rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) { // Skip the first row (headers)
            let cells = rows[i].getElementsByTagName("td");
            let planName = cells[0].textContent.toLowerCase();
            let duration = cells[2].textContent.toLowerCase();
            
            if (planName.includes(input) || duration.includes(input)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
</script>

</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
