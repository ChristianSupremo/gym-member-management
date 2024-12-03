<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = $_POST['MemberID'];
    $name = $_POST['Name'];
    $height = $_POST['Height'];
    $weight = $_POST['Weight'];
    $physicalCondition = $_POST['PhysicalCondition'];

    // Update member data
    $sql = "UPDATE Member SET Name = ?, Height = ?, Weight = ?, PhysicalCondition = ? WHERE MemberID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddsi", $name, $height, $weight, $physicalCondition, $memberID);

    if ($stmt->execute()) {
        echo "Member updated successfully.";
        header("Location: view_members.php");
    } else {
        echo "Error updating member: " . $conn->error;
    }
}
?>
