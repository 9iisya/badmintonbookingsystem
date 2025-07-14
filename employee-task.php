<?php
require_once 'db_connection.php';

// Fetch task + employee info
$query = "
    SELECT 
        task.Task_ID AS id, 
        task.Task_Name AS task_name, 
        employee.Emp_Name AS assigned_to, 
        task.Task_Status AS status 
    FROM task 
    JOIN employee ON task.Task_Assigned_To = employee.EMPLOYEE_ID
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: white;
        }
        header {
            background-color: #004aad;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            position: relative;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        nav ul {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 5px;
            transition: 0.3s;
        }
        nav ul li a:hover, nav ul li a.active {
            background-color: white;
            color: #004aad;
        }
        .hamburger {
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 2rem;
            top: 70px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 160px;
            z-index: 999;
        }
        .dropdown-menu a {
            display: block;
            padding: 10px 15px;
            color: black;
            text-decoration: none;
        }
        .dropdown-menu a:hover {
            background-color: #f1f1f1;
        }
        .show { 
            display: block !important; 
        }
        main {
            padding: 2rem;
        }
        h2 {
            margin-bottom: 1rem;
            color: #004aad;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #004aad;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        select {
            padding: 5px;
        }
        .update-btn {
            padding: 5px 12px;
            background-color: #004aad;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .update-btn:hover {
            background-color: #003080;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">EMPLOYEE DASHBOARD</div>
    <nav>
        <ul>
            <li><a href="employee-court-schedule.php">COURT SCHEDULE</a></li>
            <li><a href="employee-court-status.php">COURT STATUS</a></li>
            <li><a href="employee-task.php" class="active">TASK</a></li>
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Log Out</a>
    </div>
</header>

<main>
    <h2>Task List</h2>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Assigned To</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['task_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['assigned_to']); ?></td>
                    <td>
                        <form method="POST" action="employee-task-status.php" onsubmit="return confirmStatusChange(this)">
                            <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                            <select name="status">
                                <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="In Progress" <?php if ($row['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                <option value="Completed" <?php if ($row['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                            </select>
                            <button type="submit" class="update-btn">Update</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }

    function confirmStatusChange(form) {
        var statusSelect = form.elements['status'];
        var selectedStatus = statusSelect.options[statusSelect.selectedIndex].text;
        var confirmation = confirm("Are you sure you want to change the task status to \"" + selectedStatus + "\"?");
        return confirmation;
    }
</script>

<?php include 'footer.php'; ?>

</body>
</html>

