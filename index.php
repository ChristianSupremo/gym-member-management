<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System - Dashboard</title>
    <style>
        /* Existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #C0C0C0;
        }
        h1, h2 {
            text-align: center;
        }
        .dashboard {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .card {
            border: 4px solid #333333;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            background-color: #008080;
            width: 200px;
            cursor: pointer;
        }
        .card:hover {
            background-color: #FFF275;
            border: 4px solid #FFF275;
            font-weight: bold;
            color: #191970;
        }
        /* Content area styles */
        #content-area {
            margin-top: 20px;
            padding: 20px;
            background-color: #36454F;
            border: 2px solid #333333;
            border-radius: 8px;
            /* color: #BA8E23; */
        }
        .ccard {
            color: #BA8E23;
        }
    </style>
</head>
<body>
    <h1>Welcome to New You Fitness Club</h1>
    <h2>Member Management Dashboard</h2>

    <div class="dashboard">
        <div class="card" onclick="loadContent('register_member.php')">
            <h3>Register New Member</h3>
            <p class="ccard">Go to Registration</p>
        </div>
        <div class="card" onclick="loadContent('member_management.php')">
            <h3>Manage Members</h3>
            <p class="ccard">View and Edit members and their Plans</p>
        </div>
        <div class="card" onclick="loadContent('payment_management.php')">
            <h3>Track Payments</h3>
            <p class="ccard">Record Payments</p>
        </div>
        <div class="card" onclick="loadContent('view_plans.php')">
            <h3>View Plans</h3>
            <p class="ccard">View Assigned Plans</p>
        </div>
        <div class="card" onclick="loadContent('view_payments.php')">
            <h3>View Payments</h3>
            <p class="ccard">View Payment History</p>
        </div>
    </div>

    <!-- Content area where PHP files will be loaded -->
    <div id="content-area">
        <p>Select an option from above to view details here.</p>
    </div>

    <script>
        function loadContent(page) {
            // Create an XMLHttpRequest object
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Insert the content into the content-area div
                    document.getElementById("content-area").innerHTML = this.responseText;
                }
            };
            // Open and send the request
            xhttp.open("GET", page, true);
            xhttp.send();
        }
    </script>
</body>
</html>
