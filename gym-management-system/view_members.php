<?php
include 'db.php'; // Database connection

// Fetch all members with their physical condition
$sql = "SELECT 
            Member.MemberID, 
            Member.Name, 
            Member.Height, 
            Member.Weight, 
            Member.PhysicalCondition
        FROM Membership
        JOIN Member ON Membership.MemberID = Member.MemberID"; // No need for Plan table
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
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #2F4F4F;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #C0C0C0;
        }
        tr:nth-child(odd) {
            background-color: white;
        }
        tr:hover {
            background-color: #D0E8C5;
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
    <input type="text" id="searchInput" placeholder="Search by Name..." onkeyup="filterTable()">
</div>

<!-- Members Table -->
<table id="membersTable">
    <thead>
        <tr>
            <th>Member ID</th>
            <th>Name</th>
            <th>Height (cm)</th>
            <th>Weight (kg)</th>
            <th>Physical Condition</th>
            <th>Actions</th>
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
                    <td><?php echo htmlspecialchars($row['PhysicalCondition']); ?></td>
                    <td>
                        <form method="GET" action="edit_member.php" style="display:inline;">
                            <input type="hidden" name="MemberID" value="<?php echo $row['MemberID']; ?>">
                            <button type="submit" style="background-color: #007BFF; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                                Edit
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No members found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


<script>
// Filter table by name
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#membersTable tbody tr');

    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        row.style.display = name.includes(input) ? '' : 'none';
    });
}
</script>

</body>
</html>

<?php
$conn->close();
?>