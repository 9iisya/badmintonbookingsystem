<?php
session_start();
if (!isset($_SESSION['cust_id'])) {
    header("Location: customer-login.php");
    exit();
}

$customerName = $_SESSION['cust_name'];
$customerEmail = $_SESSION['cust_email'];
$custId = $_SESSION['cust_id'];
$bookingDate = $_POST['booking_date'] ?? '';
$courtId = $_POST['court_type'] ?? '';
$slotTimes = $_POST['slot_times'] ?? [];
$totalAmount = count($slotTimes) * 30.00; // RM30 per slot

function generateBookingId($conn) {
    $result = $conn->query("SELECT Book_ID FROM booking ORDER BY Book_ID DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastIdNum = intval(substr($row['Book_ID'], 1));
        $newIdNum = $lastIdNum + 1;
    } else {
        $newIdNum = 1;
    }
    return "B" . str_pad($newIdNum, 3, "0", STR_PAD_LEFT);
}

function generatePaymentId($conn) {
    $result = $conn->query("SELECT Payment_ID FROM PAYMENT ORDER BY Payment_ID DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastIdNum = intval(substr($row['Payment_ID'], 4));
        $newIdNum = $lastIdNum + 1;
    } else {
        $newIdNum = 1;
    }
    return "PYMT" . str_pad($newIdNum, 4, "0", STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_times']) && isset($_POST['cardnumber'])) {

    $employeeId = 'EMP01'; // contoh Employee ID
    $conn = new mysqli('localhost', 'root', '', 'badmintondb');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($slotTimes as $slot) {
        list($checkIn, $checkOut) = explode('-', $slot);

        $bookingId = generateBookingId($conn);

        $stmt = $conn->prepare("INSERT INTO booking (Book_ID, Cust_ID, Court_ID, Employee_ID, Court_CheckInTime, Court_CheckOutTime, Court_UseDate)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $bookingId, $custId, $courtId, $employeeId, $checkIn, $checkOut, $bookingDate);
        $stmt->execute();
        $stmt->close();

        $update = $conn->prepare("UPDATE court_status SET Court_Status = 'Not Available' WHERE Court_ID = ? AND Status_Date = ? AND Time_Slot = ?");
        $update->bind_param("sss", $courtId, $bookingDate, $slot);
        $update->execute();
        $update->close();
    }

    // Insert into PAYMENT table
    $paymentId = generatePaymentId($conn);
    $today = date('Y-m-d');
    $paymentMethod = "Credit Card";

    $stmt = $conn->prepare("INSERT INTO PAYMENT (Payment_ID, Cust_ID, Payment_Date, Payment_Method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $paymentId, $custId, $today, $paymentMethod);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    // Store details in session
    $_SESSION['receipt'] = [
        'name' => $customerName,
        'email' => $customerEmail,
        'amount' => $totalAmount,
        'payment_method' => $paymentMethod,
        'payment_id' => $paymentId,
        'court' => $courtId,
        'date' => $bookingDate,
        'slots' => $slotTimes,
    ];

    $_SESSION['payment_success'] = true;
    header("Location: customer-mybooking-receipt.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <style>
        * { margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: white;
        }

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

        .hamburger {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 2rem;
            top: 70px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            min-width: 160px;
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

        .payment-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .column {
            width: 48%;
        }

        h3 {
            color: #ff8d8d;
            font-style: italic;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 2px solid black;
            border-radius: 20px;
            font-size: 1rem;
        }
        
        .row {
            display: flex;
            gap: 1rem;
        }

        .cards img {
            width: 50px;
            margin-right: 10px;
        }

        .submit-btn {
            margin-top: 20px;
            text-align: right;
        }

        .submit-btn button {
            background-color: #ff8d8d;
            color: white;
            padding: 0.7rem 2rem;
            border: none;
            border-radius: 30px;
            font-weight: bold;
            font-size: 1rem; cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn button:hover {
            background-color: #e57575;
        }

        .cancel-btn {
            background-color: #ccc;
            color: #333;
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

        @media (max-width: 768px) {
            .payment-container {
                flex-direction: column;
            }

            .column {
                width: 100%;
            }

            .submit-btn {
                text-align: center;
            }
        }
    </style>
</head>

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

<form method="post">
    <div class="payment-container">
        <div class="column">
            <h3>BILLING ADDRESS</h3>
            <label>Full Name:</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($customerName) ?>" readonly>
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($customerEmail) ?>" readonly>
            <label>Address:</label>
            <input type="text" name="address" required>
            <label>City:</label>
            <input type="text" name="city" required>
            <div class="row">
                <div>
                    <label>State:</label>
                    <input type="text" name="state" required>
                </div>
                <div>
                    <label>Post Code:</label>
                    <input type="text" name="postcode" required>
                </div>
            </div>
            <div style="margin-top: 2rem;">
                <label style="font-weight: bold; color: #ff8d8d; font-style: italic;">TOTAL AMOUNT:</label>
                <div style="font-size: 1.2rem; color: #333;">RM<?= number_format($totalAmount, 2) ?></div>
            </div>
        </div>

        <div class="column">
            <h3>PAYMENT</h3>
            <label>Cards Accepted:</label>
            <div class="cards">
                <img src="https://img.icons8.com/color/48/000000/paypal.png" />
                <img src="https://img.icons8.com/color/48/000000/mastercard.png" />
                <img src="https://img.icons8.com/color/48/000000/visa.png" />
                <img src="https://img.icons8.com/color/48/000000/discover.png" />
            </div>
            <label>Name On Card:</label>
            <input type="text" name="cardname" required>
            <label>Credit Card Number:</label>
            <input type="text" name="cardnumber" required>
            <label>Exp Month:</label>
            <input type="text" name="expmonth" required>
            <div class="row">
                <div>
                    <label>Exp Year:</label>
                    <input type="text" name="expyear" required>
                </div>
                <div>
                    <label>CVV:</label>
                    <input type="text" name="cvv" required>
                </div>
            </div>

            <input type="hidden" name="booking_date" value="<?= htmlspecialchars($bookingDate) ?>">
            <input type="hidden" name="court_type" value="<?= htmlspecialchars($courtId) ?>">
            <?php foreach ($slotTimes as $slot): ?>
                <input type="hidden" name="slot_times[]" value="<?= htmlspecialchars($slot) ?>">
            <?php endforeach; ?>

            <div class="submit-btn">
                <a href="customer-mybooking-details.php" class="cancel-btn">CANCEL</a>
                <button type="submit">SUBMIT</button>
            </div>
        </div>
    </div>
</form>

<script>
function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}
window.addEventListener("click", function(e) {
    if (!e.target.closest(".hamburger")) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>
