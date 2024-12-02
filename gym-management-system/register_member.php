<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $height = $_POST['height'];
    $weight = $_POST['weight'];

    // Get physical conditions from checkboxes and "others"
    $physical_conditions = isset($_POST['physical_condition']) ? $_POST['physical_condition'] : [];
    $other_condition = isset($_POST['other_condition']) ? trim($_POST['other_condition']) : '';

    // Combine conditions into a single string
    if (!empty($other_condition)) {
        $physical_conditions[] = $other_condition;
    }
    $physical_condition = implode(", ", $physical_conditions);

        // Validate date of birth using DateTime
    $dob = $_POST['date_of_birth'];
    $dob_check = DateTime::createFromFormat('Y-m-d', $dob);

    // Get today's date
    $today = new DateTime();

    // Check if the date is valid, matches the format, and is not in the future
    if ($dob_check === false || $dob_check->format('Y-m-d') !== $dob || $dob_check > $today) {
        // Redirect to index.php with an error message
        $_SESSION['error_message'] = "Error: Invalid Date of Birth. Please enter a valid date of birth that is not in the future.";
        header("Location: index.php");
        exit; // Stop further execution
    }

    // Validate height and weight
    if (!is_numeric($height) || $height <= 0) {
        $_SESSION['error_message'] = "Error: Please enter a valid height.";
        header("Location: index.php");
        exit;
    }

    if (!is_numeric($weight) || $weight <= 0 || $weight > 500) {
        $_SESSION['error_message'] = "Error: Please enter a valid weight (positive number, up to 500 kg).";
        header("Location: index.php");
        exit;
    }



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
        .error {
            color: red;
            font-size: 12px;
            display: none;
        }
    </style>
    <script>
        document.querySelector("form").addEventListener("submit", function(event) {
            const dob = document.getElementById("date_of_birth").value;
            const dobError = document.getElementById("dob_error");

            const height = document.getElementById("height").value;
            const weight = document.getElementById("weight").value;
            const heightError = document.getElementById("height_error");
            const weightError = document.getElementById("weight_error");

            // Validate Date of Birth
            if (!dob || dob === "0000-00-00") {
                dobError.style.display = "block";
                event.preventDefault();
            } else {
                dobError.style.display = "none";
            }

            // Validate Height (positive number)
            if (!height || isNaN(height) || height <= 0) {
                heightError.style.display = "block";
                event.preventDefault();
            } else {
                heightError.style.display = "none";
            }

            // Validate Weight (positive number, reasonable range)
            if (!weight || isNaN(weight) || weight <= 0 || weight > 500) {
                weightError.style.display = "block";
                event.preventDefault();
            } else {
                weightError.style.display = "none";
            }
        });
    </script>
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
        <span id="dob_error" class="error">Please enter a valid date of birth.</span>

        <label for="phone">Phone Number:</label>
        <input type="tel" name="phone" id="phone" placeholder="Enter phone number" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Enter email" required>

        <label for="Height">Height (cm):</label>
        <input type="text" name="height" id="height" placeholder="Enter height in centimeters" required>
        <span id="height_error" class="error">Please enter a valid height (positive number).</span>

        <label for="Weight">Weight (kg):</label>
        <input type="text" name="weight" id="weight" placeholder="Enter weight in kilograms" required>
        <span id="weight_error" class="error">Please enter a valid weight (positive number, up to 500 kg).</span>

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
