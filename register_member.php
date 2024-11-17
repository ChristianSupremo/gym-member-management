<?php
session_start();
include 'db.php';  // Include the database connection file

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zipcode = $_POST['zipcode'];
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $physical_condition = $_POST['physical_condition'];
    $plan_id = $_POST['plan_id'];

    // Insert member into the Member table
    $sql_member = "INSERT INTO Member (Name, Address, City, Province, Zipcode, Gender, DateOfBirth, PhoneNo, EmailID, PhysicalCondition)
                   VALUES ('$name', '$address', '$city', '$province', '$zipcode', '$gender', '$dob', '$phone', '$email', '$physical_condition')";

    if ($conn->query($sql_member) === TRUE) {
        $member_id = $conn->insert_id; // Get the ID of the newly inserted member
        
        // Insert the membership
        $sql_membership = "INSERT INTO Membership (MemberID, PlanID, StartDate, EndDate, PaymentDate, Status)
                   VALUES ('$member_id', '$plan_id', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), CURDATE(), 'Active')";// Default 30 days
        
        if ($conn->query($sql_membership) === TRUE) {
            $_SESSION['success_message'] = "Member successfully registered!";
            header("Location: index.php"); // Redirect to the main page
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .regLabel {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"], input[type="email"], input[type="date"], input[type="number"], input[type="tel"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }
        input[type="submit"] {
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .gender-group {
            display: flex;  
            align-items: center;  
            gap: 20px;  
            margin-top: 10px;  
        }
    </style>
</head>
<body>

    <h2 class="regLabel">Register New Member</h2>

    <form action="register_member.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" placeholder="Enter member's name" required>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" placeholder="Enter member's address" required>

        <label for="city">City:</label>
        <input type="text" name="city" id="city" placeholder="Enter city" required>

        <label for="province">Province:</label>
        <input type="text" name="province" id="province" placeholder="Enter province" required>

        <label for="zipcode">Zipcode:</label>
        <input type="text" name="zipcode" id="zipcode" placeholder="Enter zipcode" required>

        <label>Gender:</label>
        <div class="gender-group">
            <div>
                <input type="radio" name="gender" value="M" id="male" required>
                <label for="male">Male</label>
            </div>
            <div>
                <input type="radio" name="gender" value="F" id="female" required>
                <label for="female">Female</label>
            </div>
        </div>

        <label for="date_of_birth">Date of Birth:</label>
        <input type="date" name="date_of_birth" id="date_of_birth" required>

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" placeholder="Enter phone number" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Enter email" required>

        <label for="physical_condition">Physical Condition:</label>
        <input type="text" name="physical_condition" id="physical_condition" placeholder="Enter physical condition" required>

        <!-- Fetch available plans -->
        <label for="plan_id">Select Plan:</label>
        <select name="plan_id" id="plan_id" required>
            <?php
            include 'db.php';
            $plan_query = "SELECT PlanID, PlanName FROM Plan";
            $plan_result = $conn->query($plan_query);
            if ($plan_result->num_rows > 0) {
                while ($plan_row = $plan_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($plan_row['PlanID']) . "'>" . htmlspecialchars($plan_row['PlanName']) . "</option>";
                }
            } else {
                echo "<option value=''>No plans available</option>";
            }
            ?>
        </select>

        <input type="submit" value="Register">
    </form>

</body>
</html>
