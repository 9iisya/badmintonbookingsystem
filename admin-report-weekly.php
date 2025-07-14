<?php
// DB Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'badmintondb';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get date range from GET or default to current week
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime('monday this week'));
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d', strtotime('sunday this week'));

// Query based on selected date range
$sql = "
    SELECT c.Court_Desc, COUNT(b.Book_ID) AS total_bookings,
           COUNT(b.Book_ID) * c.court_RatePerSlot AS total_revenue
    FROM booking b
    JOIN court c ON b.Court_ID = c.Court_ID
    WHERE b.Court_UseDate BETWEEN '$from' AND '$to'
    GROUP BY c.Court_ID
";

$result = $conn->query($sql);
$report = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: white; }

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
            padding: 2rem;
        }

        h2 {
            text-align: center;
            color: #007d14;
            margin-bottom: 20px;
        }

        .filter-section {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-section input,
        .filter-section button {
            padding: 8px;
            font-size: 14px;
        }

        .filter-section label {
            font-weight: bold;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #007d14;
            color: white;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        .generate-btn {
            display: block;
            margin: 0 auto;
            background-color: #007d14;
            color: white;
            padding: 10px 25px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .generate-btn:hover {
            background-color: #005f0f;
        }

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
            top: -8px;
        }

        .back-button:hover {
            background-color: #003f0b;
        }

        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #d3d3d3;
            font-size: 14px;
            color: black;
            font-weight: bold;
        }

        @media print {
            header, nav, .hamburger, .dropdown-menu, .generate-btn, .filter-section, .back-button {
                display: none !important;
            }

            main {
                padding: 0;
            }

            table {
                width: 100%;
            }
        }

        .show { display: block !important; }
    </style>
</head>
<body>

<header>
    <div class="logo">ADMIN DASHBOARD</div>
    <nav>
        <ul>
            <li><a href="admin-dashboard.php">DASHBOARD</a></li>
            <li><a href="admin-employee-menu.php">EMPLOYEE</a></li>
            <li><a href="admin-customer-menu.php">CUSTOMER</a></li>
            <li><a href="admin-report-menu.php" class="active">REPORT</a></li>
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Logout</a>
    </div>
</header>

<main>
    <div class="title-bar">
        <a href="admin-report-menu.php" class="back-button">Back</a>
        <h2>📋 Weekly Report</h2>
    </div>

    <form class="filter-section" method="get">
        <label for="from">From:</label>
        <input type="date" name="from" id="from" value="<?= $from ?>" required>

        <label for="to">To:</label>
        <input type="date" name="to" id="to" value="<?= $to ?>" required>

        <button type="submit">Search</button>
    </form>

    <p style="text-align: center; margin-bottom: 20px; font-weight: bold;">
        Report Date Range: <?= date("d M Y", strtotime($from)) ?> - <?= date("d M Y", strtotime($to)) ?>
    </p>

    <table>
        <thead>
            <tr>
                <th>Court</th>
                <th>Total Bookings</th>
                <th>Total Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($report)): ?>
                <?php foreach ($report as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Court_Desc']) ?></td>
                        <td><?= $row['total_bookings'] ?></td>
                        <td><?= number_format($row['total_revenue'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No data available for this range.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button class="generate-btn" onclick="window.print()">Generate</button>
</main>

<?php include 'footer.php'; ?>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }
</script>

</body>
</html>
