<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Main Menu</title>
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
        nav ul li a:hover, 
        nav ul li a.active {
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
        .hero {
            background-image: url('homepage-main-menu-background.jpg');
            background-size: cover;
            background-position: center;
            height: 78vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-content h1 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
            font-style: italic;
        }
        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .btn {
            background-color: #1b5e20;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #2e7d32;
        }
        .show { display: block !important; }
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
            <li><a href="homepage-main-menu.php" class="active">Home</a></li>
            <li><a href="homepage-about.php">About</a></li>
            <li><a href="homepage-contact.php">Contact</a></li>
            <li><a href="homepage-review.php">Review</a></li> 
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="management-login.php">Management Login</a>
        <a href="customer-login.php">Customer Login</a>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>BADMINTON COURT BOOKING SYSTEM</h1>
        <p>Book your game. Rule the court.</p>
        <a href="customer-login.php" class="btn">Get Started</a>
    </div>
</section>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }
</script>

<?php include 'footer.php'; ?>

</body>
</html>
