<?php
session_start();
include 'db_connection.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback_id'])) {
    $feedbackID = $_POST['feedback_id'];
    $reply = htmlspecialchars(trim($_POST['reply']));
    $adminID = 'AD01'; // Fixed Admin ID

    if (!empty($reply)) {
        $stmt = $conn->prepare("UPDATE feedback SET Feedback_Reply = ?, Admin_ID = ? WHERE Feedback_ID = ?");
        $stmt->bind_param("sss", $reply, $adminID, $feedbackID);

        if ($stmt->execute()) {
            $message = "✅ Reply saved successfully.";
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "❌ Please enter a reply.";
    }
}

$result = $conn->query("SELECT * FROM feedback ORDER BY Feedback_Date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Customer Feedback</title>
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif;
    }
    html, body {
      height: 100%; background-color: #f4f4f4;
    }
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
      display: none; position: absolute; right: 2rem; top: 70px;
      background-color: #f9f9f9; border-radius: 5px;
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); min-width: 160px; z-index: 999;
    }
    .dropdown-menu a {
      display: block; padding: 10px 15px; color: black; text-decoration: none;
    }
    .dropdown-menu a:hover { background-color: #f1f1f1; }
    .show { display: block !important; }

    main { flex: 1; padding: 2rem; }
    .title-bar {
      display: flex; justify-content: center; align-items: center;
      gap: 1rem; margin: 2rem 0 1.5rem; flex-wrap: wrap;
    }
    .back-button {
      padding: 0.4rem 1rem; background-color: #005d10; color: white;
      text-decoration: none; border-radius: 6px; font-size: 14px;
      transition: background 0.3s; position: relative; top: 0px;
    }
    .back-button:hover { background-color: #003f0b; }

    h1 {
      text-align: center; color: #06402B;
    }

    .message {
      text-align: center; font-weight: bold; margin-bottom: 15px;
    }
    .message.success { color: green; }
    .message.error { color: red; }

    table {
      width: 100%; border-collapse: collapse;
      margin-top: 20px; background-color: white;
    }
    th, td {
      padding: 12px; border: 1px solid #ccc;
    }
    th {
      background-color: #007d14; color: white;
    }
    textarea {
      width: 100%; height: 80px; padding: 8px; border-radius: 5px;
    }
    .readonly { background-color: #eee; border: none; }
    .submit-btn {
      background-color: #06402B; color: white; border: none;
      padding: 6px 16px; border-radius: 5px; margin-top: 10px; cursor: pointer;
    }
    .submit-btn:hover { background-color: #086b45; }
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
    <h1>Admin Feedback Management</h1>
  </div>

  <?php if (!empty($message)): ?>
    <p class="message <?= str_contains($message, '❌') ? 'error' : 'success' ?>"><?= $message ?></p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>Feedback ID</th>
        <th>Customer ID</th>
        <th>Rating</th>
        <th>Date</th>
        <th>Comment</th>
        <th>Reply</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['Feedback_ID']) ?></td>
          <td><?= htmlspecialchars($row['Cust_ID']) ?></td>
          <td><?= htmlspecialchars($row['Feedback_Rating']) ?> ★</td>
          <td><?= htmlspecialchars($row['Feedback_Date']) ?></td>
          <td><?= htmlspecialchars($row['Feedback_Comment']) ?></td>
          <td>
            <form method="post" action="">
              <input type="hidden" name="feedback_id" value="<?= $row['Feedback_ID'] ?>">
              <textarea name="reply" <?= $row['Feedback_Reply'] ? 'readonly class="readonly"' : '' ?>><?= htmlspecialchars($row['Feedback_Reply']) ?></textarea>
              <?php if (!$row['Feedback_Reply']): ?>
                <button type="submit" class="submit-btn">Submit Reply</button>
              <?php endif; ?>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
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
