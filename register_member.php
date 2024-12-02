<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (isset($_SESSION['error_message'])) {
    // Display the error message in a dialog or alert
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";

    // Clear the error message from session after displaying it
    unset($_SESSION['error_message']);
}

include 'db.php';  // Include the database connection file

// Custom function to validate date format
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

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
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    // Validate the date of birth
    if (!validateDate($dob)) {
        $_SESSION['error_message'] = "Invalid date of birth format. Please provide a valid date (e.g., MM-DD-YYYY).";
        header("register_member.php"); // Redirect to index.php (dashboard)
        exit;
    }

    if (strtotime($dob) > time()) {
        $_SESSION['error_message'] = "Date of birth cannot be in the future.";
        header("register_member.php"); // Redirect to index.php (dashboard)
        exit;
    }


    // Get physical conditions from checkboxes and "others"
    $physical_conditions = isset($_POST['physical_condition']) ? $_POST['physical_condition'] : [];
    $other_condition = isset($_POST['other_condition']) ? trim($_POST['other_condition']) : '';

    // Combine conditions into a single string
    if (!empty($other_condition)) {
        $physical_conditions[] = $other_condition;
    }
    $physical_condition = implode(", ", $physical_conditions);

    // Insert member into the Member table
    $sql_member = "INSERT INTO Member (Name, Address, City, Province, Zipcode, Gender, DateOfBirth, PhoneNo, EmailID, PhysicalCondition, Height, Weight)
                   VALUES ('$name', '$address', '$city', '$province', '$zipcode', '$gender', '$dob', '$phone', '$email', '$physical_condition', '$height', '$weight')";

    if ($conn->query($sql_member) === TRUE) {
        $member_id = $conn->insert_id; // Get the ID of the newly inserted member
        
        // Insert a new row into Membership without PlanID
        $sql_membership = "INSERT INTO Membership (MemberID, PlanID, StartDate, EndDate, PaymentDate, Status)
                   VALUES ('$member_id', NULL, NULL, NULL, NULL, 'Inactive')";

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
    <script>
    function validateForm() {
        const dob = document.getElementById('date_of_birth').value;
        const dobDate = new Date(dob);
        const today = new Date();

        if (isNaN(dobDate.getTime()) || dobDate > today) {
            alert("Invalid date of birth. Please provide a valid past date.");
            return false;
        }
        return true;
    }

    window.onload = function() {
        <?php if (isset($_SESSION['error_message'])): ?>
            alert("<?php echo $_SESSION['error_message']; ?>");
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    }
    </script>
</head>
<body>

    <h2 class="regLabel">Register New Member</h2>

    <form action="register_member.php" method="POST" onsubmit="return validateForm();">
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

        <label for="Height">Height(cm):</label>
        <input type="text" name="height" id="height" placeholder="Enter height in centimeter" required>

        <label for="Weight">Weight(kg):</label>
        <input type="text" name="weight" id="weight" placeholder="Enter weight in kilograms" required>

        <label for="physical_condition">Physical Conditions:</label>
        <input type="checkbox" name="physical_condition[]" value="Hypertension"> Hypertension<br>
        <input type="checkbox" name="physical_condition[]" value="Diabetes"> Diabetes<br>
        <input type="checkbox" name="physical_condition[]" value="Asthma"> Asthma<br>
        <input type="checkbox" name="physical_condition[]" value="Back Pain"> Back Pain<br>
        <input type="checkbox" name="physical_condition[]" value="Heart Problems"> Heart Problems<br>
        <input type="checkbox" name="physical_condition[]" value="Arthritis"> Arthritis<br>
        <label for="other_condition">Others:</label>
        <input type="text" name="other_condition" id="other_condition" placeholder="Specify if any"><br>

        <input type="submit" value="Register">
    </form>

</body>
</html>
