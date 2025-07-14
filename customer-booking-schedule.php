<?php
session_start();
if (!isset($_SESSION['cust_id'])) {
    header("Location: customer-login.php");
    exit();
}
$customerName = $_SESSION['cust_name'];
$currentDate = date('Y-m-d');
$selectedDate = isset($_GET['date']) ? $_GET['date'] : $currentDate;

// DB connection
$conn = new mysqli('localhost', 'root', '', 'badmintondb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Dashboard - Booking Schedule</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
    body { background-color: white; }

    body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}


    header {
      background-color: #ff8d8d; color: white;
      display: flex; justify-content: space-between; align-items: center;
      padding: 1rem 2rem; position: relative;
    }
    .logo { display: flex; align-items: center; font-size: 1.5rem; font-weight: bold; }
    .logo img { height: 30px; margin-left: 10px; }
    nav ul { display: flex; list-style: none; gap: 0.5rem; }
    nav ul li a {
      color: white; text-decoration: none; font-weight: bold;
      padding: 8px 14px; border-radius: 10px; transition: 0.3s;
    }
    nav ul li a:hover, nav ul li a.active { background-color: white; color: #ff8d8d; }
    .hamburger { font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; }
    .dropdown-menu {
      display: none; position: absolute; right: 2rem; top: 70px;
      background-color: #f9f9f9; border-radius: 5px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2); min-width: 160px; z-index: 100;
    }
    .dropdown-menu a { display: block; padding: 10px 15px; color: black; text-decoration: none; }
    .dropdown-menu a:hover { background-color: #f1f1f1; }
    .show { display: block !important; }
    .dashboard-container { padding: 2rem; max-width: 1200px; margin: auto; }
    .schedule-header {
      display: flex; align-items: center; gap: 0.8rem; margin-bottom: 1.5rem;
    }
    input[type="date"] {
      padding: 0.4rem 0.8rem; border: 1.5px solid #ff8d8d;
      border-radius: 5px; width: 160px;
    }
    .schedule-table {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 2rem;
    }
    .court-column h3 { text-align: center; color: #ff8d8d; margin-bottom: 1rem; }
    table {
    width: 95%;
    margin: auto;
    border-collapse: collapse;
    font-size: 0.95rem;
    text-align: center;
    table-layout: fixed;
}

th, td {
    border: 1.5px solid #ff8d8d;
    padding: 0.6rem;
    width: 50%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}


    th { background-color: #fff0f0; color: #ff8d8d; }
    .available-link {
      color: green; font-weight: bold; text-decoration: none;
      padding: 4px 10px; border: 2px solid green;
      border-radius: 5px; transition: 0.3s; display: inline-block;
    }
    .available-link:hover { background-color: green; color: white; }
    .not-available { color: red; font-weight: bold; }
    @media (max-width: 600px) {
      .schedule-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
  </style>
</head>

<body>
<header>
  <div class="logo">CUSTOMER DASHBOARD</div>
  <nav>
    <ul>
      <li><a href="customer-booking-schedule.php" class="active">BOOKING SCHEDULE</a></li>
      <li><a href="customer-feedback.php">FEEDBACK</a></li>
    </ul>
  </nav>
  <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
  <div class="dropdown-menu" id="dropdownMenu">
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="dashboard-container">
  <p>Welcome, <?php echo htmlspecialchars($customerName); ?>!</p><br>
  <div class="schedule-header">
    <label for="booking-date"><strong>Select Date:</strong></label>
    <input type="date" id="booking-date" value="<?php echo $selectedDate; ?>"
      onchange="location.href='customer-booking-schedule.php?date=' + this.value;">
  </div>

  <div class="schedule-table">
    <?php
    // Query court descriptions
    $courtList = [];
    $resCourts = $conn->query("SELECT Court_ID, Court_Desc FROM COURT");
    while ($cr = $resCourts->fetch_assoc()) {
      $courtList[$cr['Court_ID']] = $cr['Court_Desc'];
    }

    // Query today's statuses
    $stmt = $conn->prepare("
      SELECT s.Court_ID, s.Time_Slot, s.Court_Status
      FROM COURT_STATUS s
      WHERE s.Status_Date = ?
      ORDER BY s.Court_ID, s.Time_Slot
    ");
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $stmt->bind_result($cID, $timeSlot, $cStatus);

    $courts = [];
    while ($stmt->fetch()) {
      $courts[$cID][$timeSlot] = strtolower($cStatus);
    }
    $stmt->close();

    // Define your fixed time slots
    $timeSlots = ['05:00 - 07:00', '07:00 - 09:00', '09:00 - 11:00'];

    // Display
    foreach ($courtList as $courtId => $courtDesc) {
      echo "<div class='court-column'><h3>" . htmlspecialchars($courtDesc) . "</h3>";
      echo "<table><tr><th>Time Slot</th><th>Status</th></tr>";

      foreach ($timeSlots as $time) {
        $status = isset($courts[$courtId][$time]) ? $courts[$courtId][$time] : 'not available';

        echo "<tr><td>" . htmlspecialchars($time) . "</td><td>";
        if ($status === 'available') {
          $link = "customer-mybooking-details.php?court=" . urlencode($courtId) .
                  "&date=" . urlencode($selectedDate) .
                  "&time=" . urlencode($time);
          echo "<a href='$link' class='available-link'>Available</a>";
        } else {
          echo "<span class='not-available'>Not Available</span>";
        }
        echo "</td></tr>";
      }

      echo "</table></div>";
    }

    $conn->close();
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
