<?php
session_start();

if (isset($_SESSION['error_message'])) {
    // Display the error message in a dialog or alert
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // Clear the error message after showing it
}

if (isset($_SESSION['success_message'])) {
    // Display success message in a dialog or alert
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); // Clear the success message after showing it
}
?>

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
            background-color: #C0C0C0;
        }

        .header-container {
            display: flex;
            align-items: center; /* Align items vertically */
            gap: 20px; /* Add spacing between elements */
            background-color: #2F4F4F; /* Darker background color */
            padding: 20px; /* Add some padding inside the container */
            border-radius: 8px; /* Rounded corners (optional) */
            flex-direction: column; /* Stack elements vertically */
        }

        h1 {
            margin: 0; /* Remove default margin */
            border: 2px solid #333; /* Box around the h1 */
            padding: 10px; /* Add padding inside the h1 box */
            border-radius: 5px; /* Rounded corners for the h1 box (optional) */
            background-color: #4B4B4B; /* Optional background color for h1 */
            color: #FFF; /* Make the text color white for contrast */
        }

        h2 {
            margin: 0; /* Remove default margin */
            color: #FDFD96;
            font-size: 18px;
        }

        .dashboard {
            display: flex;
            flex-wrap: wrap; /* Ensure cards wrap to a new row if they don't fit */
            justify-content: center; /* Center cards horizontally */
            gap: 20px; /* Space between cards */
            margin-top: 20px; /* Add space above the dashboard */
            max-width: 100%; /* Ensure the dashboard stays within the viewport */
            overflow: hidden; /* Prevent cards from spilling outside */
            padding: 20px; /* Optional: Add padding to the dashboard area */
            border-radius: 8px; /* Optional: Rounded corners for the dashboard */
        }

        @media (max-width: 768px) {
            .dashboard {
                flex-direction: column; /* Stack cards vertically on small screens */
                align-items: center; /* Center align cards */
            }
            .card {
                width: 90%; /* Cards take up most of the screen width */
            }
        }


        .card {
            border: 4px solid #333333;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            background-color: #008080;
            width: 200px;
            height: 90px;
            cursor: pointer;
        }

        .card:hover {
            background-color: #FFF275;
            border: 4px solid #FFF275;
            color: #191970; Change the text color of direct children elements
        }

        .card:hover .ccard {
            color: #191970; /* Ensure that .ccard elements also change color */
        }

        #content-area {
            margin-top: 20px;
            padding: 20px;
            background-color: #36454F;
            border: 2px solid #333333;
            border-radius: 8px;
        }

        .ccard {
            color: #FDFD96;
        }
        
    </style>
</head>
<body>
    <div class="header-container">
        <h1>New You Fitness Club</h1>
        <h2>Member Management Dashboard</h2>

        <div class="dashboard">
            <div class="card" onclick="loadContent('register_member.php')">
                <h3>Register New Member</h3>
                <p class="ccard">Go to Registration</p>
            </div>
            <div class="card" onclick="loadContent('member_management.php')">
                <h3>Manage Members</h3>
                <p class="ccard">Manage Member Payments</p>
            </div>
            <div class="card" onclick="loadContent('view_members.php')">
            <h3>View Users</h3>
            <p class="ccard">View Members and Conditions</p>
            </div>

            <div class="card" onclick="loadContent('view_plans.php')">
                <h3>Manage Plans</h3>
                <p class="ccard">View and Manage Plans</p>
            </div>
            <div class="card" onclick="loadContent('view_payments.php')">
                <h3>View Payments</h3>
                <p class="ccard">View Payment History</p>
            </div>
        </div>
    </div>

    <!-- Content area where PHP files will be loaded -->
    <div id="content-area">
        <p>Select an option from above to view details here.</p>
    </div>

    <?php if (!empty($success_message)): ?>
        <script>
            alert("<?php echo addslashes($success_message); ?>");
        </script>
    <?php endif; ?>

    <script>
    function loadContent(page) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("content-area").innerHTML = this.responseText;

            // Re-attach the filtering logic
            attachSearchFilter();
        }
    };
    xhttp.open("GET", page, true);
    xhttp.send();
}

// Re-attach search functionality after content load
function attachSearchFilter() {
    const input = document.getElementById("searchInput");
    if (input) {
        input.removeEventListener("keyup", searchHandler); // Remove existing listeners to avoid duplicates
        input.addEventListener("keyup", function() {
            filterTable("paymentsTable"); // Call filterTable with the correct table ID
        });
    }
}


    // Function to handle form submission via AJAX
    function attachFormSubmitHandler() {
        const paymentForm = document.getElementById("payment-form");
        if (paymentForm) {
            paymentForm.onsubmit = function(event) {
                event.preventDefault(); // Prevent the default form submission

                const formData = new FormData(paymentForm);
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        alert(this.responseText); // Show success or error message from PHP
                        loadContent('payment_management.php'); // Reload content to reflect changes
                    }
                };
                xhttp.open("POST", "payment_management.php", true);
                xhttp.send(formData);
            };
        }
    }
    
    function filterTable() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("membersTable");
    const rows = table.getElementsByTagName("tr");

    // Loop through all rows in the table (skip the header row)
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName("td");

        let match = false;

        // Only check the second column (Name)
        const nameCell = cells[1]; // Index 1 corresponds to the Name column
        if (nameCell && nameCell.textContent.toLowerCase().indexOf(filter) > -1) {
            match = true;
        }

            // Show or hide the row based on the search result
            if (match) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    function ftb() {
    const input = document.getElementById("searchInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("paymentsTable");
    const rows = table.getElementsByTagName("tr");

    // Loop through all rows in the table (skip the header row)
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName("td");

        let match = false;

        // Check the second column (Name) and third column (Payment Date)
        const nameCell = cells[1]; // Index 1 corresponds to the Name column
        const dateCell = cells[2]; // Index 2 corresponds to the Payment Date column

        // Check if either the name or payment date matches the search input
        if (
            (nameCell && nameCell.textContent.toLowerCase().includes(filter)) ||
            (dateCell && dateCell.textContent.toLowerCase().includes(filter))
        ) {
            match = true;
        }

        // Show or hide the row based on the search result
            if (match) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    }
    function filterByStatus() {
    const activeCheckbox = document.getElementById("activeCheckbox");
    const inactiveCheckbox = document.getElementById("inactiveCheckbox");
    const table = document.getElementById("membersTable");
    const rows = table.getElementsByTagName("tr");

    // Enforce mutual exclusivity
    if (activeCheckbox.checked && inactiveCheckbox.checked) {
        inactiveCheckbox.checked = false; // Auto-uncheck the other
    }

    for (let i = 1; i < rows.length; i++) {
        const statusCell = rows[i].querySelector("td:nth-child(7)"); // Update selector for 'Status' column
        if (statusCell) {
            const status = statusCell.textContent.trim().toLowerCase();
            if (
                (activeCheckbox.checked && status === "active") ||
                (inactiveCheckbox.checked && status === "inactive") ||
                (!activeCheckbox.checked && !inactiveCheckbox.checked) // Show all if none checked
            ) {
                rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }

    function filterPayments() {
        const thisMonthCheckbox = document.getElementById("thisMonthCheckbox");
        const sixMonthsCheckbox = document.getElementById("sixMonthsCheckbox");
        const thisYearCheckbox = document.getElementById("thisYearCheckbox");
        const table = document.getElementById("paymentsTable");
        const rows = table.getElementsByTagName("tr");

        // Enforce mutual exclusivity
        if (thisMonthCheckbox.checked && (sixMonthsCheckbox.checked || thisYearCheckbox.checked)) {
            sixMonthsCheckbox.checked = false;
            thisYearCheckbox.checked = false;
        } else if (sixMonthsCheckbox.checked && thisYearCheckbox.checked) {
            thisYearCheckbox.checked = false;
        }

        const today = new Date();
        for (let i = 1; i < rows.length; i++) {
            const dateCell = rows[i].querySelector("td:nth-child(3)");
            if (dateCell) {
                const paymentDate = new Date(dateCell.textContent.trim());
                const yearDifference = today.getFullYear() - paymentDate.getFullYear();
                const monthDifference =
                    today.getMonth() - paymentDate.getMonth() + yearDifference * 12;

                let showRow = false;

                if (
                    thisMonthCheckbox.checked &&
                    today.getFullYear() === paymentDate.getFullYear() &&
                    today.getMonth() === paymentDate.getMonth()
                ) {
                    showRow = true;
                } else if (sixMonthsCheckbox.checked && monthDifference <= 6) {
                    showRow = true;
                } else if (thisYearCheckbox.checked && yearDifference === 0) {
                    showRow = true;
                }

                rows[i].style.display = showRow ? "" : "none";
            }
        }
    }

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

    // Open the payment modal and populate hidden fields with MemberID and PlanID
    function openPaymentModal(memberId, memberName, planId) {
        document.getElementById('paymentModal').style.display = 'block';
        document.getElementById('modalMemberID').value = memberId;
        document.getElementById('modalPlanID').value = planId;
        document.getElementById('memberName').innerText = memberName; // Set the member's name in the modal
    }

    // Close the modal
    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    function ADDvalidatePlan(form) {
        const rate = form.rate.value;
        const duration = form.duration.value;

        // Check for empty fields and zero values in the Add Plan form
        if (rate <= 0 || duration <= 0) {
            alert("Rate and Duration must be greater than 0.");
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }

    function validatePlan(form) {
        const rate = form.rate.value;
        const duration = form.duration.value;

        // Check for empty fields and zero values in the Edit Plan form
        if (rate <= 0 || duration <= 0) {
            alert("Rate and Duration must be greater than 0.");
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }

</script>

</body>
</html>
