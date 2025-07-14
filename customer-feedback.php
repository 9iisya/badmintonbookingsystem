<?php
session_start();

if (!isset($_SESSION['cust_id'])) {
    header("Location: customer-login.php");
    exit();
}

$customerName = $_SESSION['cust_name'];
$custId = $_SESSION['cust_id'];
$thankYou = "";
$error = "";
$today = date('Y-m-d');

$conn = new mysqli('localhost', 'root', '', 'badmintondb');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;
    $comment = trim($_POST['comment'] ?? '');

    if ($rating >= 1 && $rating <= 5) {

        // Generate next Feedback_ID
        $result = $conn->query("SELECT Feedback_ID FROM feedback ORDER BY Feedback_ID DESC LIMIT 1");

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastId = $row['Feedback_ID'];
            $num = intval(substr($lastId, 2)) + 1;
            $newId = "FB" . str_pad($num, 4, '0', STR_PAD_LEFT);
        } else {
            $newId = "FB0001";
        }

        $stmt = $conn->prepare("INSERT INTO feedback (Feedback_ID, Cust_ID, Feedback_Rating, Feedback_Comment, Feedback_Date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $newId, $custId, $rating, $comment, $today);

        if ($stmt->execute()) {

            // Check for plural 'stars'
            if ($rating > 1) {
                $starText = "stars";
            } else {
                $starText = "star";
            }

            // Message based on rating
            if ($rating <= 3) {
                $thankYou = "Thank you for rating us " . $rating . " " . $starText . "! We appreciate your feedback and will strive to improve.";
            } else {
                $thankYou = "Thank you for rating us " . $rating . " " . $starText . "! We're glad you're satisfied with our service!";
            }

        } else {
            $error = "Error saving feedback. Please try again.";
        }

        $stmt->close();
    } else {
        $error = "Please select a rating before submitting.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Feedback</title>
  <style>
    * { margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: Arial, sans-serif; }
      html, body {
  height: 100%;
  display: flex;
  flex-direction: column;
}

body {
  margin: 0;
}

main.feedback-container {
  flex: 1; /* This will make the main area grow and push footer to bottom */
}

    body { 
      background-color: white; }

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

    .top-nav {
      display: flex;
      list-style: none;
      gap: 0.5rem;
    }

    .top-nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      padding: 8px 14px;
      border-radius: 10px;
      transition: 0.3s;
    }

    .top-nav a:hover,
    .top-nav a.active {
      background-color: white;
      color: #ff8d8d;
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

    .show { 
      display: block !important;
    }

    .logout-btn {
      position: absolute;
      top: 1rem;
      right: 1rem;
      background-color: white;
      color: #ff8d8d;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      text-decoration: none;
      font-weight: bold;
    }

    .logout-btn:hover {
      background-color: #ffe6e6;
    }

    main.feedback-container {
      max-width: 600px;
      margin: 3rem auto;
      background-color: white;
      padding: 2rem 3rem;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }

    h2.feedback-title {
      color: #ff8d8d;
      margin-bottom: 1.5rem;
    }

    .rating {
      display: flex;
      justify-content: center;
      flex-direction: row; /* left to right */
      gap: 15px;
      font-size: 3rem;
      cursor: pointer;
      user-select: none;
    }

    .rating input {
      display: none;
    }

    .rating label {
      color: lightgray;
      transition: color 0.3s ease;
    }

    /* Hover and selected effect */
    .rating input:checked ~ label,
    .rating label:hover,
    .rating label:hover ~ label {
      color: #ff8d8d;
    }

    /* Fix highlight order for left-to-right */
    .rating input:checked + label,
    .rating input:checked ~ label {
      color: #ff8d8d;
    }

    button.submit-btn {
      margin-top: 2rem;
      padding: 10px 40px;
      font-size: 1.2rem;
      font-weight: bold;
      background-color: #ff8d8d;
      border: none;
      border-radius: 30px;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button.submit-btn:hover {
      background-color: #e57575;
    }

    p.error-message {
      color: red;
      margin-top: 1rem;
    }

    p.thank-you {
      color: #228822;
      font-size: 1.3rem;
      font-weight: bold;
      margin-top: 2rem;
    }

    @media (max-width: 600px) {
      .top-nav {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
      }
    }
  </style>
</head>

<body>
<header>
  <div class="logo">CUSTOMER DASHBOARD</div>
  <nav>
    <ul class="top-nav">
      <li><a href="customer-booking-schedule.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customer-booking-schedule.php' ? 'active' : ''; ?>">BOOKING SCHEDULE</a></li>
      <li><a href="customer-feedback.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customer-feedback.php' ? 'active' : ''; ?>">FEEDBACK</a></li>
    </ul>
  </nav>
  <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
  <div class="dropdown-menu" id="dropdownMenu">
    <a href="logout.php">Logout</a>
  </div>
</header>

<main class="feedback-container">
  <h2 class="feedback-title">RATE OUR SERVICE</h2>

<?php if (!empty($thankYou)) : ?>
  <p class="thank-you"><?= htmlspecialchars($thankYou) ?></p>
<?php else: ?>
  <form method="post" action="">
    <div class="rating">
      <?php
        for ($i = 1; $i <= 5; $i++) {
            $checked = (isset($_POST['rating']) && intval($_POST['rating']) === $i) ? 'checked' : '';
            echo <<<HTML
<input type="radio" id="star{$i}" name="rating" value="{$i}" {$checked} />
<label for="star{$i}" title="{$i} star">&#9733;</label>
HTML;
        }
      ?>
    </div>

    <label for="comment" style="margin-top: 1.5rem; display: block; color: #555; font-weight: bold;">Your Feedback</label>
    <textarea name="comment" id="comment" rows="5" placeholder="Share your thoughts..." style="width: 100%; padding: 10px; margin-top: 0.5rem; border-radius: 8px; border: 1px solid #ccc; resize: vertical;"><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>

    <?php if (!empty($error)) : ?>
      <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <button type="submit" class="submit-btn">SUBMIT</button>
  </form>
<?php endif; ?>
</main>

<?php include 'footer.php'; ?>

<script>
  const stars = document.querySelectorAll('.rating label');
  stars.forEach(label => {
    label.addEventListener('mouseenter', () => {
      highlightStars(label.getAttribute('for'));
    });
    label.addEventListener('mouseleave', () => {
      resetStars();
    });
    label.addEventListener('click', () => {
      resetStars();
      highlightStars(document.querySelector('input[name="rating"]:checked').id);
    });
  });

  function highlightStars(starId) {
    let starValue = parseInt(starId.replace('star', ''));
    stars.forEach(label => {
      let labelValue = parseInt(label.getAttribute('for').replace('star', ''));
      label.style.color = labelValue <= starValue ? '#ff8d8d' : 'lightgray';
    });
  }

  function resetStars() {
    const checkedStar = document.querySelector('input[name="rating"]:checked');
    if (checkedStar) {
      highlightStars(checkedStar.id);
    } else {
      stars.forEach(label => label.style.color = 'lightgray');
    }
  }

  function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('show');
  }

  window.addEventListener("click", function (e) {
    const dropdown = document.getElementById("dropdownMenu");
    if (!e.target.closest(".hamburger")) {
      dropdown?.classList.remove("show");
    }
  });

  window.onload = resetStars;
</script>


</body>
</html>
