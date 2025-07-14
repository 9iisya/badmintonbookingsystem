<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: white;
        }
        header {
            background-color: #007d14;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            position: relative;
        }
        .logo {
            display: flex;
            align-items: center;
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
        nav ul li a:hover, 
        nav ul li a.active {
            background-color: white;
            color: #007d14;
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

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem 0; 
        }

        img {
            height: 300px; 
        }

        main > div {
            gap: 2rem; 
        }

        .custom-btn {
            width: 220px; 
            padding: 0.6rem 1rem;
            background-color: #007d14;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 1rem;
            display: inline-block;
            text-align: center;
        }
        .custom-btn:hover {
            background-color: #2e7d32;
        }
        .icon {
            width: 40px;
            height: 40px;
        }

        .show { display: block !important; }

        .title-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0 1.5rem;
            flex-wrap: wrap;
        }

        .back-button {
            padding: 0.4rem 1rem;
            background-color: #005d10;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.3s;
            position: relative;
            top: 1px;
        }

        .back-button:hover {
            background-color: #003f0b;
        }

    </style>
</head>
<body>

<header>
    <div class="logo">
        ADMIN DASHBOARD
    </div>
    <nav>
        <ul>
            <li><a href="admin-dashboard.php">DASHBOARD</a></li>
            <li><a href="admin-employee-menu.php" class="active">EMPLOYEE</a></li>
            <li><a href="admin-customer-menu.php">CUSTOMER</a></li>
            <li><a href="admin-report-menu.php">REPORT</a></li> 
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Logout</a>
    </div>
</header>
<?php
require_once 'db_connection.php';

// Get employee list
$employeeQuery = "SELECT EMPLOYEE_ID, Emp_Name FROM employee";
$employees = mysqli_query($conn, $employeeQuery);

// Get task logs
$taskQuery = "
    SELECT task.Task_ID, task.Task_Name, employee.Emp_Name, task.Task_Status 
    FROM task 
    JOIN employee ON task.Task_Assigned_To = employee.EMPLOYEE_ID 
    ORDER BY task.Task_ID DESC
";
$taskLogs = mysqli_query($conn, $taskQuery);
?>

<main>
    <div style="width: 90%; margin: auto;">
        <div class="title-bar">
    <a href="admin-employee-menu.php" class="back-button">Back</a>
    <h2 style="color: #007d14;">Employee Task Logs</h2>
</div>


        <!-- Task Assignment Form -->
        <form method="POST" action="employee-assign-task.php" style="margin-bottom: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="task_name" placeholder="Task Name" required style="padding: 8px; flex: 1;">
            <select name="employee_id" required style="padding: 8px;">
                <option value="" disabled selected>Assign to...</option>
                <?php while ($emp = mysqli_fetch_assoc($employees)) { ?>
                    <option value="<?= $emp['EMPLOYEE_ID'] ?>"><?= htmlspecialchars($emp['Emp_Name']) ?></option>
                <?php } ?>
            </select>
            <button type="submit" style="padding: 8px 16px; background-color: #007d14; color: white; border: none; border-radius: 4px;">Assign Task</button>
        </form>

        <!-- Task Log Table -->
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #007d14; color: white;">
                    <th style="padding: 10px;">Task Name</th>
                    <th style="padding: 10px;">Assigned To</th>
                    <th style="padding: 10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($taskLogs)) { ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;"><?= htmlspecialchars($row['Task_Name']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($row['Emp_Name']) ?></td>
                        <td style="padding: 10px;"><?= htmlspecialchars($row['Task_Status']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }
</script>

</body>
</html>
