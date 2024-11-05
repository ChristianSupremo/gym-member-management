<?php
include 'db.php';  // Database connection

// Fetch all members and plans from the database
$members = $conn->query("SELECT MemberID, Name FROM Member");
$plans = $conn->query("SELECT PlanID, PlanName FROM Plan");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $plan_id = $_POST['plan_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "INSERT INTO Membership (MemberID, PlanID, StartDate, EndDate, Status) 
            VALUES ('$member_id', '$plan_id', '$start_date', '$end_date', 'Active')";

    if ($conn->query($sql) === TRUE) {
        echo "Plan assigned successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Fitness Plan</title>
</head>
<body>
    <h2>Assign Fitness Plan to Member</h2>
    <form method="POST" action="">
        <label for="member_id">Select Member:</label><br>
        <select name="member_id" required>
            <?php
            while($row = $members->fetch_assoc()) {
                echo "<option value='" . $row['MemberID'] . "'>" . $row['Name'] . "</option>";
            }
            ?>
        </select><br>

        <label for="plan_id">Select Plan:</label><br>
        <select name="plan_id" required>
            <?php
            while($row = $plans->fetch_assoc()) {
                echo "<option value='" . $row['PlanID'] . "'>" . $row['PlanName'] . "</option>";
            }
            ?>
        </select><br>

        <label for="start_date">Start Date:</label><br>
        <input type="date" name="start_date" required><br>

        <label for="end_date">End Date:</label><br>
        <input type="date" name="end_date" required><br><br>

        <input type="submit" value="Assign Plan">
    </form>
</body>
</html>
