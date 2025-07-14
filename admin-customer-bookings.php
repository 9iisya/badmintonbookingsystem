<?php
include 'db_connection.php';

$dateFilter = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Bookings</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
    html, body { height: 100%; }
    body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f4f4f4; }

    header {
      background-color: #007d14; color: white;
      display: flex; justify-content: space-between; align-items: center;
      padding: 1rem 2rem; position: relative;
    }
    .logo { font-size: 1.5rem; font-weight: bold; }
    nav ul { display: flex; list-style: none; gap: 1.5rem; }
    nav ul li a {
      color: white; text-decoration: none; font-weight: bold;
      padding: 8px 16px; border-radius: 5px; transition: 0.3s;
    }
    nav ul li a:hover, nav ul li a.active {
      background-color: white; color: #007d14;
    }
    .hamburger {
      font-size: 1.5rem; cursor: pointer; display: flex; align-items: center;
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
      display: block; padding: 10px 15px; color: black; text-decoration: none;
    }
    .dropdown-menu a:hover { background-color: #f1f1f1; }
    .show { display: block !important; }

    main { flex: 1; padding: 2rem; }

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
      top: 0px;
    }

    .back-button:hover {
      background-color: #003f0b;
    }

    form.date-form {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 1.5rem;
      gap: 10px;
      flex-wrap: wrap;
    }

    form.date-form input[type="date"] {
      padding: 0.4rem;
      font-size: 14px;
    }

    form.date-form button {
      padding: 0.4rem 1rem;
      background-color: #007d14;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    form.date-form button:hover {
      background-color: #005d10;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 2rem;
    }

    th, td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }

    th {
      background-color: #007d14;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

<header>
  <div class="logo">ADMIN DASHBOARD</div>
  <nav>
    <ul>
      <li><a href="admin-dashboard.php">DASHBOARD</a></li>
      <li><a href="admin-employee-menu.php">EMPLOYEE</a></li>
      <li><a href="admin-customer-menu.php" class="active">CUSTOMER</a></li>
      <li><a href="admin-report-menu.php">REPORT</a></li>
    </ul>
  </nav>
  <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
  <div class="dropdown-menu" id="dropdownMenu">
    <a href="logout.php">Logout</a>
  </div>
</header>

<main>
  <div class="title-bar">
    <a href="admin-customer-menu.php" class="back-button">Back</a>
    <h2>Customer Booking History</h2>
  </div>

  <form class="date-form" method="GET" action="">
    <label for="date">View Bookings by Date:</label>
    <input type="date" id="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>">
    <button type="submit">Search</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Customer Name</th>
        <th>Court</th>
        <th>Booking Date</th>
        <th>Time Slot</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "
        SELECT 
          c.Cust_Name, 
          co.Court_Desc, 
          b.Court_UseDate, 
          CONCAT(DATE_FORMAT(b.Court_CheckInTime, '%H:%i'), ' - ', DATE_FORMAT(b.Court_CheckOutTime, '%H:%i')) AS Time_Slot
        FROM BOOKING b
        JOIN CUSTOMER c ON b.Cust_ID = c.Cust_ID
        JOIN COURT co ON b.Court_ID = co.Court_ID
      ";

      if (!empty($dateFilter)) {
        $sql .= " WHERE b.Court_UseDate = '$dateFilter'";
      }

      $sql .= " ORDER BY b.Court_UseDate DESC, b.Court_CheckInTime ASC";

      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$row['Cust_Name']}</td>
                  <td>{$row['Court_Desc']}</td>
                  <td>{$row['Court_UseDate']}</td>
                  <td>{$row['Time_Slot']}</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='4'>No bookings found.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</main>

<script>
function toggleDropdown() {
  document.getElementById('dropdownMenu').classList.toggle('show');
}
</script>
<?php include 'footer.php'; ?>

</body>
</html>
