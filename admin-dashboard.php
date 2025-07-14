<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'badmintondb';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$year = date('Y');
$month = date('m');
$totalBookings = $conn->query("SELECT COUNT(*) as total FROM BOOKING WHERE YEAR(Court_UseDate) = $year AND MONTH(Court_UseDate) = $month")->fetch_assoc()['total'] ?? 0;
$totalRevenue = $totalBookings * 30;
$totalVisitors = $conn->query("SELECT COUNT(DISTINCT Cust_ID) as total FROM BOOKING WHERE YEAR(Court_UseDate) = $year AND MONTH(Court_UseDate) = $month")->fetch_assoc()['total'] ?? 0;

// Prepare chart data
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$dailyBookings = array_fill(1, $daysInMonth, 0);
$result = $conn->query("SELECT DAY(Court_UseDate) AS day, COUNT(*) AS total FROM BOOKING WHERE YEAR(Court_UseDate) = $year AND MONTH(Court_UseDate) = $month GROUP BY DAY(Court_UseDate)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dailyBookings[(int)$row['day']] = (int)$row['total'];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        html, body { height: 100%; overflow-y: hidden; }
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: white; }

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

        main { flex: 1; padding: 1rem; display: flex; gap: 1rem; }

        .left-side {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .right-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: center;
            gap: 1rem;
        }

        .cards { display: flex; flex-direction: column; gap: 1rem; align-items: center; }
        .card {
            background-color: #007d14; color: white;
            padding: 10px 15px; border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            width: 150px; text-align: center;
        }
        .card h3 { margin-bottom: 5px; font-size: 14px; }
        .card p { font-size: 16px; font-weight: bold; }

        .info-boxes { display: flex; flex-direction: column; gap: 1rem; align-items: center; }
        .info-box {
            background-color: #f2f2f2;
            padding: 6px 10px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-weight: bold;
            font-size: 12px;
            width: 150px; 
            text-align: center;
        }

        .chart-container {
            width: 100%;
            max-width: 700px; 
            height: 420px;    
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            border-radius: 10px;
            padding: 10px;
        }

        .show { display: block !important; }
    </style>
</head>
<body>

<header>
    <div class="logo">ADMIN DASHBOARD</div>
    <nav>
        <ul>
            <li><a href="admin-dashboard.php" class="active">DASHBOARD</a></li>
            <li><a href="admin-employee-menu.php">EMPLOYEE</a></li>
            <li><a href="admin-customer-menu.php">CUSTOMER</a></li>
            <li><a href="admin-report-menu.php">REPORT</a></li> 
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Logout</a>
    </div>
</header>

<main>
    <div class="left-side">
        <div class="chart-container">
            <h3 style="text-align:center; margin-bottom: 5px; font-size: 16px;">📊 Bookings - <?php echo date('F Y'); ?></h3>
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

   <div class="right-side">
    <div class="cards" style="gap: 1.5rem;">

        <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
            <div class="card">
                <h5>Total Bookings</h5>
                <p><?php echo $totalBookings; ?> <span style="font-size: 10px;">Booking sessions</span></p>
            </div>
            <div class="info-box">Daily bookings of this month</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
            <div class="card">
                <h5>Total Revenue</h5>
                <p><span style="font-size: 12px;">RM </span><?php echo number_format($totalRevenue, 2); ?></p>
            </div>
            <div class="info-box">Daily earning of this month</div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: center;">
            <div class="card">
                <h5>Total Visitors</h5>
                <p><?php echo $totalVisitors; ?> <span style="font-size: 10px;">Visitor</span></p>
            </div>
            <div class="info-box">Daily visitors of this month</div>
        </div>

    </div>
</div>
</main>

<?php include 'footer.php'; ?>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }

    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($dailyBookings)); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode(array_values($dailyBookings)); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: '#4CAF50',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Bookings' } },
                x: { title: { display: true, text: 'Day' } }
            }
        }
    });
</script>

</body>
</html>
