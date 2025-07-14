<?php
session_start();

if (!isset($_SESSION['cust_id'])) {
    header("Location: customer-login.php");
    exit();
}

$fullName = $_SESSION['cust_name'];
$email = $_SESSION['cust_email'];
$date = isset($_GET['date']) ? $_GET['date'] : '';
$court = isset($_GET['court']) ? $_GET['court'] : '';
$time = isset($_GET['time']) ? $_GET['time'] : '';

// DB Connection
$conn = new mysqli('localhost', 'root', '', 'badmintondb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch courts from DB
$courtList = array();
$courtRes = $conn->query("SELECT Court_ID, Court_Desc FROM court");
while ($row = $courtRes->fetch_assoc()) {
    $courtList[$row['Court_ID']] = $row['Court_Desc'];
}

// Fetch available slots for selected court & date
$availableSlots = array();
if (!empty($date) && !empty($court)) {
    $stmt = $conn->prepare("SELECT Time_Slot FROM court_status WHERE Court_ID = ? AND Status_Date = ? AND Court_Status = 'available'");
    $stmt->bind_param("ss", $court, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $availableSlots[] = $row['Time_Slot'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Bookings</title>
  <style>
    * { margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: Arial, sans-serif; }
      
    body { 
      background-color: white;
    }

    header.customer-header,
    header {
      background-color: #ff8d8d;
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

    .form-wrapper {
      padding: 3rem 2rem;
    }

    form {
      width: 450px;
      padding: 1rem;
    }

    h2 {
      color: #ff8d8d;
      margin-bottom: 1rem;
    }

    label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
      color:rgb(218, 95, 95);
    }

    input, select {
      width: 100%;
      padding: 0.6rem;
      border: none;
      background: #ffe1e1;
      border-radius: 20px;
      color: #444;
    }

    input[readonly] {
      background-color: #ffe1e1;
      color: #777;
    }

    .slot-group {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

    .add-btn {
      padding: 0.4rem 0.8rem;
      background: #ff8d8d;
      color: white;
      border: none;
      border-radius: 50%;
      font-size: 1.2rem;
      line-height: 1;
      cursor: pointer;
    }

    .buttons {
      margin-top: 2rem;
      display: flex;
      justify-content: space-between;
    }

    .buttons button {
      background: #ff8d8d;
      color: white;
      padding: 0.7rem 2rem;
      border: none;
      border-radius: 30px;
      font-weight: bold;
      cursor: pointer;
    }

    .cancel-btn {
      background-color: #ccc !important; 
      color: #333 !important;
      padding: 0.7rem 2rem;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      margin-left: 1rem;
      display: inline-block;
      transition: background-color 0.3s ease;
    }

    .cancel-btn:hover {
      background-color: #999;
    }

    .center-box {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .booking-form {
      background-color: #fff0f0;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 600px;
    }

    select option:disabled {
      color: #ccc;
    }
  </style>
</head>
<body>

<body>
<header>
  <div class="logo">CUSTOMER DASHBOARD</div>
  <nav>
    <ul>
      <li><a href="customer-booking-schedule.php" class="active">MY BOOKING</a></li>
      <li><a href="customer-feedback.php">FEEDBACK</a></li>
    </ul>
  </nav>
  <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
  <div class="dropdown-menu" id="dropdownMenu">
    <a href="logout.php">Logout</a>
  </div>
</header>

<div class="form-wrapper">
  <div class="center-box">
      <form method="POST" action="customer-mybooking-payment.php">
      <h2 style="color: #ff8d8d; text-align: left;">Booking Details</h2>

      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($fullName) ?>" readonly>

      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>

      <label>Date</label>
      <input type="date" name="booking_date" value="<?= htmlspecialchars($date ?? date('Y-m-d')) ?>" required onchange="updatePage()">

      <label>Slot Time</label>
      <div id="slot-container">
        <div class="slot-group">
          <select name="slot_times[]">
            <?php if (count($availableSlots) > 0): ?>
              <?php foreach ($availableSlots as $slot): ?>
                <option value="<?= htmlspecialchars($slot) ?>" <?= $slot == $time ? 'selected' : '' ?>>
                  <?= htmlspecialchars($slot) ?>
                </option>
              <?php endforeach; ?>
            <?php else: ?>
              <option value="" disabled selected>No available slots</option>
            <?php endif; ?>
          </select>
          <button type="button" class="add-btn" onclick="addSlot()">+</button>
        </div>
      </div>

      <label>Court Type</label>
      <select name="court_type" required onchange="updatePage()">
        <option value="C01" <?= $court == 'C01' ? 'selected' : '' ?>>Court A</option>
        <option value="C02" <?= $court == 'C02' ? 'selected' : '' ?>>Court B</option>
        <option value="C03" <?= $court == 'C03' ? 'selected' : '' ?>>Court C</option>
      </select>

      <div class="buttons" style="margin-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 1rem;">
          <button type="button" class="cancel-btn" onclick="window.location.href='customer-booking-schedule.php'">CANCEL</button>
          <button type="submit" id="confirmBtn" style="background: #ff8d8d; color: white; padding: 0.7rem 2rem; border: none; border-radius: 30px; font-weight: bold; cursor: pointer;">
            CONFIRM
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function syncAvailableOptions() {
    const allSelects = document.querySelectorAll("select[name='slot_times[]']");
    const selectedValues = Array.from(allSelects).map(sel => sel.value);

    allSelects.forEach(select => {
      const currentValue = select.value;

      Array.from(select.options).forEach(option => {
        if (option.value && selectedValues.includes(option.value) && option.value !== currentValue) {
          option.disabled = true;
        } else {
          option.disabled = false;
        }
      });
    });
  }

  function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('show');
  }

  function addSlot() {
    const slotContainer = document.getElementById('slot-container');
    const newSlotGroup = document.createElement('div');
    newSlotGroup.classList.add('slot-group');

    const select = document.querySelector('select[name="slot_times[]"]').cloneNode(true);
    select.value = "";
    select.addEventListener("change", syncAvailableOptions);

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.textContent = '-';
    removeBtn.classList.add('add-btn'); // Apply same styling as add button
    removeBtn.onclick = function () {
      slotContainer.removeChild(newSlotGroup);
      syncAvailableOptions();
    };

    newSlotGroup.appendChild(select);
    newSlotGroup.appendChild(removeBtn);
    slotContainer.appendChild(newSlotGroup);

    syncAvailableOptions();
  }


  function updatePage() {
    const date = document.querySelector("input[name='booking_date']").value;
    const court = document.querySelector("select[name='court_type']").value;
    const url = new URL(window.location.href);
    url.searchParams.set("date", date);
    url.searchParams.set("court", court);
    window.location.href = url.toString();
  }

  function updateConfirmButtonState() {
    const slotSelects = document.querySelectorAll("select[name='slot_times[]']");
    const confirmBtn = document.getElementById("confirmBtn");
    let valid = false;

    slotSelects.forEach(select => {
      if (select.value && !select.disabled) {
        valid = true;
      }
    });

    confirmBtn.disabled = !valid;
    confirmBtn.style.opacity = valid ? "1" : "0.5";
    confirmBtn.style.cursor = valid ? "pointer" : "not-allowed";
  }

  document.addEventListener("DOMContentLoaded", function () {
    // Initial check
    updateConfirmButtonState();

    // Check every time a slot is selected or changed
    const slotSelects = document.querySelectorAll("select[name='slot_times[]']");
    slotSelects.forEach(select => {
      select.addEventListener("change", updateConfirmButtonState);
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    const firstSelect = document.querySelector("select[name='slot_times[]']");
    if (firstSelect) {
      firstSelect.addEventListener("change", syncAvailableOptions);
      syncAvailableOptions();
    }
  });

  window.addEventListener("click", function (e) {
    const dropdown = document.getElementById("dropdownMenu");
    if (!e.target.closest(".hamburger")) {
      dropdown.classList.remove("show");
    }
  });
</script>

<?php include 'footer.php'; ?>

</body>
</html>
