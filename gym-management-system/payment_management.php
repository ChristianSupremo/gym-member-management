<?php
include 'db.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id']; // Capture Member ID
    $payment_method_id = $_POST['payment_method_id']; // Capture Payment Method
    $payment_date = $_POST['payment_date']; // Capture Payment Date
    $plan_id = $_POST['plan_id']; // Capture Plan ID

    // Input validation
    if (empty($member_id) || empty($payment_method_id) || empty($payment_date) || empty($plan_id)) {
        echo "<div class='alert error'>Error: All fields are required.</div>";
        exit;
    }

    // Fetch the plan details to get the rate and duration
    $plan_check = $conn->query("SELECT PlanID, Rate, Duration FROM Plan WHERE PlanID = '$plan_id'");
    if ($plan_check->num_rows === 0) {
        echo "<div class='alert error'>Error: Invalid Plan ID.</div>";
        exit;
    }
    $plan_data = $plan_check->fetch_assoc();
    $plan_rate = $plan_data['Rate'];
    $plan_duration = $plan_data['Duration']; // Assuming Duration is in days

    // Check if the member is inactive and set the Start Date accordingly
    $sql = "SELECT Status, StartDate, EndDate FROM Membership WHERE MemberID = '$member_id'";
    $result = $conn->query($sql);
    $membership_data = $result->fetch_assoc();

    // If the status is inactive or StartDate is missing, set the StartDate to the payment date
    if ($membership_data['Status'] == 'inactive' || !$membership_data['StartDate']) {
        $start_date = $payment_date;
        // Update the membership status to active
        $update_status_sql = "UPDATE Membership SET Status = 'active', StartDate = '$start_date' WHERE MemberID = '$member_id'";
        $conn->query($update_status_sql);
    } else {
        // If the member is already active, use the current StartDate
        $start_date = $membership_data['StartDate'];
    }

    // Calculate the End Date
    if ($membership_data['EndDate']) {
        // If the member has an existing EndDate, add the plan duration to it
        $end_date = date('Y-m-d', strtotime($membership_data['EndDate'] . " + $plan_duration days"));
    } else {
        // If no EndDate exists, set the EndDate based on the PaymentDate
        $end_date = date('Y-m-d', strtotime($payment_date . " + $plan_duration days"));
    }

    // Insert the payment record into the Payment table
    $status = "Completed"; // Default to Completed if all checks pass
    $payment_sql = "INSERT INTO Payment (MembershipID, PaymentMethodID, Amount, PaymentDate, Status)
                    VALUES ('$member_id', '$payment_method_id', '$plan_rate', '$payment_date', '$status')";

    if ($conn->query($payment_sql) === TRUE) {
        // Update the Membership table with the new EndDate and PaymentDate (latest payment date)
        $update_membership_sql = "UPDATE Membership SET EndDate = '$end_date', PaymentDate = '$payment_date' 
                                  WHERE MemberID = '$member_id'";

        if ($conn->query($update_membership_sql) === TRUE) {
            echo "<div class='alert'>Payment recorded successfully! Membership updated.</div>";
        } else {
            echo "<div class='alert error'>Error updating membership with payment date and end date: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='alert error'>Error recording payment: " . $conn->error . "</div>";
    }

    exit;
}
?>
