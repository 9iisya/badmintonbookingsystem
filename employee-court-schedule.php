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

$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch court status for the selected date
$courts = [];
$stmt = $conn->prepare("
    SELECT Court_ID, Time_Slot, Court_Status
    FROM COURT_STATUS
    WHERE Status_Date = ?
    ORDER BY Court_ID, Time_Slot
");
$stmt->bind_param("s", $selectedDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $courts[$row['Court_ID']][$row['Time_Slot']] = $row['Court_Status'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Employee Dashboard - Court Schedule</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
    body { background-color: white; }
    header {
        background-color: #004aad; color: white;
        display: flex; justify-content: space-between; align-items: center;
        padding: 1rem 2rem; position: relative;
    }
    .logo { font-size: 1.5rem; font-weight: bold; }
    nav ul { display: flex; list-style: none; gap: 1.5rem; }
    nav ul li a {
        color: white; text-decoration: none; font-weight: bold;
        padding: 8px 16px; border-radius: 5px; transition: 0.3s;
    }
    nav ul li a:hover, nav ul li a.active { background-color: white; color: #004aad; }
    .hamburger { font-size: 1.5rem; cursor: pointer; }
    .dropdown-menu {
        display: none; position: absolute; right: 2rem; top: 70px;
        background-color: #f9f9f9; border-radius: 5px;
        box-shadow: 0px 8px 16px rgba(0,0,0,0.2); min-width: 160px; z-index: 100;
    }
    .dropdown-menu a {
        display: block; padding: 10px 15px; color: black; text-decoration: none;
    }
    .dropdown-menu a:hover { background-color: #f1f1f1; }
    .show { display: block !important; }
    .dashboard-container { padding: 2rem; max-width: 1200px; margin: auto; }
    .schedule-header {
        display: flex; align-items: center; gap: 0.8rem; margin-bottom: 1.5rem;
    }
    input[type="date"] {
        padding: 0.4rem 0.8rem;
        border: 1.5px solid #004aad;
        border-radius: 5px;
        width: 160px;
    }
    .schedule-table {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 2rem;
    }
    .court-column h3 { 
        text-align: center; 
        color: #004aad; 
        margin-bottom: 1rem; 
    }
    table {
        width: 95%; margin: auto;
        border-collapse: collapse; text-align: center;
        font-size: 0.9rem;
    }
    th {
        border: 1.5px solid #004aad;
        padding: 0.6rem;
        background-color: #e9f0ff;
        color: #004aad;
    }
    td {
        border: 1.5px solid #004aad;
        padding: 0.6rem;
    }
    .green { color: green; font-weight: bold; }
    .red { color: red; font-weight: bold; }
    @media (max-width: 600px) {
        .schedule-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>
</head>

<body>

<header>
    <div class="logo">EMPLOYEE DASHBOARD</div>
    <nav>
        <ul>
            <li><a href="employee-court-schedule.php" class="active">COURT SCHEDULE</a></li>
            <li><a href="employee-court-status.php">COURT STATUS</a></li>
            <li><a href="employee-task.php">TASK</a></li>
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Log Out</a>
    </div>
</header>

<div class="dashboard-container">
    <p>Welcome, <?php echo htmlspecialchars($employeeName); ?>!</p><br>

    <div class="schedule-header">
        <label for="date"><strong>Select Date:</strong></label>
        <input type="date" id="date" name="date" value="<?php echo $selectedDate; ?>"
            onchange="location.href='employee-court-schedule.php?date=' + this.value;">
    </div>

    <div class="schedule-table">
        <?php
        $courtNames = ['C01' => 'Court A', 'C02' => 'Court B', 'C03' => 'Court C'];
        $timeSlots = ['05:00 - 07:00', '07:00 - 09:00', '09:00 - 11:00'];

        foreach ($courtNames as $courtId => $courtName) {
            echo "<div class='court-column'>";
            echo "<h3>$courtName</h3>";
            echo "<table><tr><th>Time Slot</th><th>Status</th></tr>";

            foreach ($timeSlots as $slot) {
                $status = isset($courts[$courtId][$slot]) ? $courts[$courtId][$slot] : 'not available';

                if (strtoupper($status) === 'AVAILABLE') {
                    echo "<tr><td>$slot</td><td class='green'>Available</td></tr>";
                } else {
                    echo "<tr><td>$slot</td><td class='red'>Not Available</td></tr>";
                }
            }

            echo "</table></div>";
        }
        ?>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('show');
}
</script>

<?php include 'footer.php'; ?>

</body>
</html>
