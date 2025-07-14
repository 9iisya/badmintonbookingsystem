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

// Default values for month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Use selected month and year if set
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : $currentMonth;
$selectedYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;

// Query for monthly report
$sql = "
    SELECT c.Court_Desc, COUNT(b.Book_ID) AS total_bookings,
           COUNT(b.Book_ID) * c.court_RatePerSlot AS total_revenue
    FROM booking b
    JOIN court c ON b.Court_ID = c.Court_ID
    WHERE MONTH(b.Court_UseDate) = $selectedMonth AND YEAR(b.Court_UseDate) = $selectedYear
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
    <title>Monthly Report</title>
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

        .report-date {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .filter-form {
            text-align: center;
            margin-bottom: 30px;
        }

        select, button {
            padding: 8px 12px;
            font-size: 14px;
            margin-right: 8px;
        }

        table {
            width: 80%;
            margin: 0 auto 40px;
            border-collapse: collapse;
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
        }

        @media print {
            header, nav, .hamburger, .dropdown-menu, .generate-btn, .filter-form, .back-button {
                display: none !important;
            }

            table {
                width: 100%;
            }

            .report-date {
                display: block;
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
        <h2>📅 Monthly Report</h2>
    </div>

    <div class="filter-form">
        <form method="GET" action="">
            <select name="month" required>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $monthLabel = date('F', mktime(0, 0, 0, $m, 10));
                    $selected = ($m == $selectedMonth) ? 'selected' : '';
                    echo "<option value='$m' $selected>$monthLabel</option>";
                }
                ?>
            </select>

            <select name="year" required>
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                    $selected = ($y == $selectedYear) ? 'selected' : '';
                    echo "<option value='$y' $selected>$y</option>";
                }
                ?>
            </select>

            <button type="submit">Search</button>
        </form>
    </div>

    <?php
    if ($selectedMonth && $selectedYear) {
        $monthName = date('F', mktime(0, 0, 0, $selectedMonth, 10));
        echo "<p class='report-date'>Report for: $monthName $selectedYear</p>";
    }
    ?>

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
                <tr><td colspan="3">No data available for this period.</td></tr>
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
