<?php
session_start();
include 'db.php';

// Handle POST request for updating the member
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $memberID = $_POST['MemberID'];
    $name = $_POST['Name'];
    $height = $_POST['Height'];
    $weight = $_POST['Weight'];
    $physicalCondition = $_POST['PhysicalCondition'];

    // Update the member details in the database
    $sql = "UPDATE Member SET Name = ?, Height = ?, Weight = ?, PhysicalCondition = ? WHERE MemberID = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sddsi", $name, $height, $weight, $physicalCondition, $memberID);

        if ($stmt->execute()) {
            // Set success message
            $_SESSION['success_message'] = "Member details updated successfully.";
        } else {
            // Set error message
            $_SESSION['error_message'] = "Failed to update member details.";
        }
        $stmt->close();
    } else {
        // Set error message for query preparation failure
        $_SESSION['error_message'] = "An error occurred while preparing the update query.";
    }

    // Redirect to index.php
    header("Location: index.php");
    exit();
}

// Fetch member details if a valid MemberID is provided
$member = null;
if (isset($_GET['MemberID'])) {
    $memberID = $_GET['MemberID'];

    $sql = "SELECT * FROM Member WHERE MemberID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $memberID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $member = $result->fetch_assoc();
    } else {
        $_SESSION['error_message'] = "Member not found.";
        header("Location: view_members.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #C0C0C0;
        }
        .form-container {
            margin-top: 20px;
            background-color: #36454F;
            padding: 20px;
            border-radius: 8px;
            color: #FFF;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #CCC;
        }
        button {
            padding: 10px 20px;
            background-color: #008080;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #FFF275;
            color: #191970;
        }
    </style>
</head>
<body>
    <!-- Edit form -->
    <div class="form-container">
        <h2>Edit Member</h2>
        <?php if ($member): ?>
        <form method="POST">
            <input type="hidden" name="MemberID" value="<?php echo htmlspecialchars($member['MemberID']); ?>">
            <label for="Name">Name:</label>
            <input type="text" name="Name" value="<?php echo htmlspecialchars($member['Name']); ?>" required>
            <label for="Height">Height (cm):</label>
            <input type="number" step="0.01" name="Height" value="<?php echo htmlspecialchars($member['Height']); ?>" required>
            <label for="Weight">Weight (kg):</label>
            <input type="number" step="0.01" name="Weight" value="<?php echo htmlspecialchars($member['Weight']); ?>" required>
            <label for="PhysicalCondition">Physical Condition:</label>
            <input type="text" name="PhysicalCondition" value="<?php echo htmlspecialchars($member['PhysicalCondition']); ?>" required>
            <button type="submit">Update</button>
        </form>
        <?php else: ?>
        <p>No member data available to edit.</p>
        <?php endif; ?>
    </div>

    <?php
    // Display any session messages
    if (isset($_SESSION['error_message'])) {
        echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
        unset($_SESSION['error_message']);
    }
    if (isset($_SESSION['success_message'])) {
        echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
        unset($_SESSION['success_message']);
    }
    ?>
</body>
</html>
