<?php include 'db_connection.php'; ?>
<!DOCTYPE html>
<html lang="en"> 
<head>
  <meta charset="UTF-8">
  <title>Customer Reviews</title>
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
    header {
      background-color: #1b5e20;
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
    .logo img {
      height: 30px;
      margin-left: 10px; 
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
    nav ul li a:hover, nav ul li a.active {
      background-color: white;
      color: #1b5e20;
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
    .show { display: block !important; }

    .content {
      padding: 3rem 2rem;
      text-align: center;
    }
    .content h2 {
      font-size: 2.5rem;
      color: #06402B;
      margin-bottom: 1.5rem;
    }
    form.search-form {
      margin-bottom: 2rem;
    }
    form.search-form input[type="number"] {
      padding: 0.6rem;
      font-size: 1rem;
      width: 80px;
    }
    .review-box {
      background: #f3f3f3;
      padding: 1.5rem;
      border-radius: 10px;
      margin: 1.2rem auto;
      max-width: 800px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: left;
    }
    .review-box h4 {
      margin: 0;
      color: #1b5e20;
    }
    .review-box .stars {
      color: #f39c12;
      font-size: 1.1rem;
      margin: 0.5rem 0;
    }
    .review-box p {
      margin: 0.5rem 0;
    }

.page-title {
    font-size: 3rem;
    font-weight: 900;
    margin-bottom: 1.5rem;
    color: #06402B;
    font-style: italic;
    text-align: center;
}


  </style>
</head>
<body>

<header>
  <div class="logo">
    BADMINTON
    <img src="homepage-main-menu-shuttlecock-symbol.png" alt="Logo">
  </div>
  <nav>
    <ul>
      <li><a href="homepage-main-menu.php">Home</a></li>
      <li><a href="homepage-about.php">About</a></li>
      <li><a href="homepage-contact.php">Contact</a></li>
      <li><a href="homepage-review.php" class="active">Review</a></li> 
    </ul>
  </nav>
  <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
  <div class="dropdown-menu" id="dropdownMenu">
    <a href="management-login.php">Management Login</a>
    <a href="customer-login.php">Customer Login</a>
  </div>
</header>

<div class="content">
<h5 class="page-title">Customer Feedback</h5>

  <form class="search-form" method="GET" action="">
    <label for="rating">Filter by Rating:</label>
    <input type="number" id="rating" name="rating" min="1" max="5" value="<?= isset($_GET['rating']) ? htmlspecialchars($_GET['rating']) : '' ?>">
    <button type="submit">Search</button>
  </form>

  <?php
    $ratingFilter = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
    $sql = "SELECT * FROM feedback WHERE Feedback_Reply IS NOT NULL";
    if ($ratingFilter >= 1 && $ratingFilter <= 5) {
        $sql .= " AND Feedback_Rating = $ratingFilter";
    }
    $sql .= " ORDER BY Feedback_Date DESC";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
  ?>
      <div class="review-box">
        <h4>Customer ID: <?= htmlspecialchars($row['Cust_ID']) ?></h4>
        <div class="stars">Rating: <?= str_repeat('★', intval($row['Feedback_Rating'])) ?></div>
        <p><strong>Feedback:</strong> <?= htmlspecialchars($row['Feedback_Comment']) ?></p>
        <p><strong>Admin Reply:</strong> <?= htmlspecialchars($row['Feedback_Reply']) ?></p>
      </div>
  <?php
      endwhile;
    else:
      echo "<p>No feedback found.</p>";
    endif;
    $conn->close();
  ?>
</div>

<script>
  function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('show');
  }
</script>

<?php include 'footer.php'; ?>
</body>
</html>
