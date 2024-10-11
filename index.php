<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System - Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        h1 {
            text-align: center;
        }
        .dashboard {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .card {
            border: 1px solid #ccc;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            background-color: #f9f9f9;
            width: 200px;
        }
        .card a {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <h1>Welcome to New You Fitness Club</h1>
    <h2>Member Management Dashboard</h2>

    <div class="dashboard">
        <div class="card">
            <h3>Register New Member</h3>
            <a href="register_member.php">Go to Registration</a>
        </div>
        <div class="card">
            <h3>Manage Members</h3>
            <a href="member_management.php">View and Edit members and their Plans</a>
        </div>
        <div class="card">
            <h3>Track Payments</h3>
            <a href="payment_management.php">Record Payments</a>
        </div>
        <div class="card">
            <h3>View Plans</h3>
            <a href="view_plans.php">View Assigned Plans</a>
        </div>
        <div class="card">
            <h3>View Payments</h3>
            <a href="view_payments.php">View Payment History</a>
        </div>
    </div>
</body>
</html>
