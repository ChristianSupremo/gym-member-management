<?php
include 'db.php'; // Database connection

// Fetch all members along with their current plan details
$sql = "SELECT Member.MemberID, Member.Name, Member.Height, Member.Weight, Membership.Status, Plan.PlanName
        FROM Membership
        JOIN Member ON Membership.MemberID = Member.MemberID
        JOIN Plan ON Membership.PlanID = Plan.PlanID";  // Assuming Membership table has PlanID that links to Plan table
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    echo "<p>Error fetching members: " . htmlspecialchars($conn->error) . "</p>";
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members</title>
    <style>
        /* Existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px; /* Adds space between the tables */
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #2F4F4F; /* Changed for better visibility */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #C0C0C0; /* Zebra striping for readability */
        }
        tr:nth-child(odd) {
            background-color: white; /* White background for odd rows */
        }
        tr:hover {
            background-color: #D0E8C5; /* Highlight row on hover */
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar input {
            padding: 8px;
            font-size: 16px;
            width: 300px;
        }
    </style>
</head>
<body>

<h2>View All Members</h2>

<!-- Search Bar -->
<div class="search-bar">
    <input type="text" id="searchInput" placeholder="Search by Name or Status..." onkeyup="filterTable()">
</div>

<!-- Members Table -->
<table id="membersTable">
    <thead>
        <tr>
            <th>Member ID</th>
            <th>Name</th>
            <th>Height (cm)</th>
            <th>Weight (kg)</th>
            <th>Plan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['MemberID']); ?></td>
                    <td><?php echo htmlspecialchars($row['Name']); ?></td>
                    <td><?php echo htmlspecialchars($row['Height']); ?></td>
                    <td><?php echo htmlspecialchars($row['Weight']); ?></td>
                    <td><?php echo htmlspecialchars($row['PlanName']); ?></td>
                    <td><?php echo htmlspecialchars($row['Status']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No members found.</td>  <!-- Adjusted colspan to account for the new column -->
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- JavaScript -->
<script>
// Declare filterTable function globally
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

        // Check if any cell in the row contains the search query
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
        }

        // Show or hide the row based on the search result
        if (match) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>