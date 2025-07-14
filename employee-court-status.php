<?php
session_start();

if (!isset($_SESSION['employee_id'])) {
    header("Location: management-login.php");
    exit();
}

$employeeName = $_SESSION['employee_name'];

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'badmintondb';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['status_date'];
    $statusA = $_POST['court_a'];
    $statusB = $_POST['court_b'];
    $statusC = $_POST['court_c'];

    $courts = [
        'C01' => $statusA,
        'C02' => $statusB,
        'C03' => $statusC
    ];

    $timeSlots = ['05:00 - 07:00', '07:00 - 09:00', '09:00 - 11:00'];

    $success = true;

    foreach ($courts as $courtId => $courtStatus) {
        foreach ($timeSlots as $slot) {
            // Check if record exists
            $checkStmt = $conn->prepare("SELECT Status_ID FROM COURT_STATUS WHERE Court_ID = ? AND Status_Date = ? AND Time_Slot = ?");
            $checkStmt->bind_param("sss", $courtId, $date, $slot);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                // Update existing record
                $updateStmt = $conn->prepare("UPDATE COURT_STATUS SET Court_Status = ? WHERE Court_ID = ? AND Status_Date = ? AND Time_Slot = ?");
                $updateStmt->bind_param("ssss", $courtStatus, $courtId, $date, $slot);
                if (!$updateStmt->execute()) {
                    $success = false;
                }
                $updateStmt->close();
            } else {
                // Insert new record
                $insertStmt = $conn->prepare("INSERT INTO COURT_STATUS (Court_ID, Status_Date, Time_Slot, Court_Status) VALUES (?, ?, ?, ?)");
                $insertStmt->bind_param("ssss", $courtId, $date, $slot, $courtStatus);
                if (!$insertStmt->execute()) {
                    $success = false;
                }
                $insertStmt->close();
            }
            $checkStmt->close();
        }
    }

    $conn->close();

    if ($success) {
        echo "<script>alert('All court statuses updated successfully for $date'); window.location.href = 'employee-court-status.php';</script>";
    } else {
        echo "<script>alert('Some errors occurred while saving. Please try again.');</script>";
    }

}
?>


<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard - Court Status</title>
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

        .container {
            width: 500px; 
            margin: 41px auto;
            padding: 15px 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #004aad;
            text-align: center;
            margin-bottom: 12px;
            font-size: 18px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 4px;
            font-size: 13px;
        }
        input[type="date"], select {
            width: 100%;
            padding: 7px;
            font-size: 13px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        button {
            background-color: #004aad;
            color: white;
            padding: 9px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            display: block;
            margin: 10px auto 0;
            font-size: 13px;
        }
        button:hover {
            background-color: #003580;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">EMPLOYEE DASHBOARD</div>
    <nav>
        <ul>
            <li><a href="employee-court-schedule.php">COURT SCHEDULE</a></li>
            <li><a href="employee-court-status.php" class="active">COURT STATUS</a></li>
            <li><a href="employee-task.php">TASK</a></li>
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Log Out</a>
    </div>
</header>

<div class="container">
    <h2>Update Court Status</h2>
    <form method="POST">
        <label for="status_date">Select Date</label>
        <input type="date" name="status_date" required>

        <label for="court_a">Court A</label>
        <select name="court_a" id="court_a">
            <option value="AVAILABLE">Court is Available</option>
            <option value="NOT AVAILABLE">Court is Not Available</option>
        </select>

        <label for="court_b">Court B</label>
        <select name="court_b" id="court_b">
            <option value="AVAILABLE">Court is Available</option>
            <option value="NOT AVAILABLE">Court is Not Available</option>
        </select>

        <label for="court_c">Court C</label>
        <select name="court_c" id="court_c">
            <option value="AVAILABLE">Court is Available</option>
            <option value="NOT AVAILABLE">Court is Not Available</option>
        </select>

        <button type="submit">Save</button>
    </form>
</div>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }
</script>

<?php include 'footer.php'; ?>

</body>
</html>
