<?php
session_start();

$showSuccess = false;
if (isset($_SESSION['payment_success'])) {
    $showSuccess = true;
    unset($_SESSION['payment_success']); // show only once
}

if (isset($_SESSION['receipt'])) {
    $receipt = $_SESSION['receipt'];
} else {
    header("Location: customer-mybooking-payment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Receipt</title>
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

    .customer-header {
      background-color: #ff8d8d;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem; /* Ensure height matches */
      position: relative;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
    }

    nav ul {
      display: flex;
      list-style: none;
      gap: 0.5rem;
    }
    
    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 8px 14px;
      border-radius: 10px;
      transition: 0.3s;
    }
    
    nav ul li a:hover, nav ul li a.active {
      background-color: white;
      color: #ff8d8d;
    }

    .menu-wrapper {
      position: relative;
    }

    .hamburger {
      font-size: 1.5rem;
      cursor: pointer; 
      color: white;
    }

    .dropdown-menu {
      display: none;
      position: absolute; 
      right: 2rem; 
      top: 70px;
      background-color: #f9f9f9; 
      border-radius: 5px; 
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      min-width: 160px; 
      z-index: 100;
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

    .dropdown-menu.show {
      display: block !important;
    }

    .receipt-box {
      background-color: white;
      width: 400px;
      margin: 3rem auto;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .receipt-box h2 {
      text-align: center;
      margin-bottom: 1rem;
    }

    .receipt-box p {
      margin: 6px 0;
    }

    .bold {
      font-weight: bold;
    }

    hr {
      margin: 1rem 0;
      border: none;
      border-top: 1px solid #ccc;
    }

    .booking-details {
      margin-top: 1rem;
    }

    .button-row {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .btn {
      padding: 0.5rem 1rem;
      background-color: white;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 5px;
      cursor: pointer;
    }

    .btn img {
      height: 16px;
    }

    .footer-btn {
      display: flex;
      justify-content: flex-end;
      padding: 2rem;
    }

    .next-btn {
      background-color: #ff8d8d;
      color: white;
      padding: 0.7rem 2rem;
      border: none;
      border-radius: 25px;
      font-size: 1.2rem;
      font-weight: bold;
      cursor: pointer;
    }

    .next-btn:hover {
      background-color: #e57777;
    }

    @media print {
      body * {
        visibility: hidden;
      }

      .receipt-box, .receipt-box * {
        visibility: visible;
      }

      .receipt-box {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        background: white !important;
        box-shadow: none !important;
        border: none !important;
      }

      .button-row {
        display: none !important;
      }
    }
  </style>
</head>
<body>
<?php if ($showSuccess): ?>
  <script>
    alert("Payment Successful!");
  </script>
<?php endif; ?>

<header class="customer-header">
  <div class="logo">CUSTOMER DASHBOARD</div>
  <nav>
    <ul>
      <li><a href="customer-booking-schedule.php" class="active">MY BOOKING</a></li>
      <li><a href="customer-feedback.php">FEEDBACK</a></li>
    </ul>
  </nav>
  <div class="menu-wrapper">
    <div class="hamburger" onclick="toggleDropdown()">☰</div>
    <div class="dropdown-menu" id="dropdownMenu">
      <a href="logout.php">Logout</a>
    </div>
  </div>
</header>

<div class="receipt-box">
  <h2>RECEIPT</h2>
  <div style="text-align: center;">
    <p class="bold"><?php echo htmlspecialchars($receipt['name']); ?></p>
    <p><?php echo htmlspecialchars($receipt['email']); ?></p>
  </div>

  <hr>

  <p><span class="bold">Payment ID</span> <span style="float:right;"><?php echo $receipt['payment_id']; ?></span></p>
  <p><span class="bold">Total Paid</span> <span style="float:right;">RM<?php echo number_format($receipt['amount'], 2); ?></span></p>
  <p><img src="https://img.icons8.com/color/24/000000/visa.png"/> Paid By <span class="bold"><?php echo $receipt['payment_method']; ?></span></p>

  <hr>

  <div class="booking-details">
    <p class="bold">Booking Details</p>
    <p>Court <span style="float:right;"><?php echo $receipt['court']; ?></span></p>
    <p>Date <span style="float:right;"><?php echo $receipt['date']; ?></span></p>
    <p>Time <span style="float:right;"><?php echo implode(', ', $receipt['slots']); ?></span></p>
  </div>

  <hr>

  <p>This receipt serves as proof of payment.<br>Non-refundable unless canceled 24-hours before the booking.</p>

  <div class="button-row">
    <button class="btn" onclick="window.print()">
      <img src="https://img.icons8.com/ios-filled/24/000000/print.png"/> Print Receipt
    </button>
  </div>
</div>

<div class="footer-btn">
  <a href="customer-feedback.php">
    <button class="next-btn">NEXT</button>
  </a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".hamburger")?.addEventListener("click", function () {
    document.getElementById("dropdownMenu")?.classList.toggle("show");
  });

  window.addEventListener("click", function (e) {
    if (!e.target.closest(".menu-wrapper")) {
      document.getElementById("dropdownMenu")?.classList.remove("show");
    }
  });
});
</script>

<?php include 'footer.php'; ?>

</body>
</html>

<?php
unset($_SESSION['receipt']);
?>
