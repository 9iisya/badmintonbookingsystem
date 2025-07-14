<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Organizational Info</title>
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

        .hero {
            background-color: white;
            height: 78vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 2rem;
            color: #333;
            text-align: center;
        }

        .hero-content {
            display: flex;
            max-width: 900px;
            width: 100%;
            justify-content: center;
            align-items: flex-start;
            gap: 2rem;
        }

        .left-side {
            flex: 1;
            text-align: center;
        }

        .left-side h1 {
            font-size: 2.2rem;
            font-weight: 900;
            margin-bottom: 1rem;
            color: #06402B;
            font-style: italic;
        }

        .left-side img {
            max-width: 150px;
            margin-bottom: 1rem;
        }

        .left-side p {
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 0.5rem;
        }

        .right-side {
            flex: 1;
            text-align: left;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .info-box {
            border: 1.5px solid #06402B;
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
            background-color: #f9f9f9;
            margin-bottom: 0.7rem;
        }

        .btn {
            background-color: #1b5e20;
            color: white;
            padding: 0.6rem 1.1rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            margin-top: 1rem;
            display: inline-block;
            font-size: 0.85rem;
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
            <li><a href="homepage-main-menu.php">Home</a></li>
            <li><a href="homepage-about.php"class="active">About</a></li>
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
        <div class="left-side">
            <h1>Organizational Chart</h1>
            <img src="homepage-learn-more-chart.png" alt="Organizational Chart">
            <p>Our organizational structure ensures smooth operations and a welcoming environment for all visitors. From court management to event planning, each role is designed to uphold the standards and passion for badminton that defines Dewan Badminton PDTJ.</p>
        <a href="homepage-about.php" class="btn">Back to Home</a>
        </div>
        <div class="right-side">
            <div class="info-box">
                <strong>Overview</strong><br>
                Dewan Badminton PDTJ, located in Taman Maju, Jasin, Melaka, is managed by Kelab Kebajikan Dan Rekreasi PDTJ and owned by Pejabat Daerah dan Tanah Jasin. Since 2010, it has been a hub for community sports and recreation. The court is equipped with quality lighting, nets, and smooth flooring, welcoming players of all ages and levels.
            </div>
            <div class="info-box">
                <strong>Vision</strong><br>
                To be the best place for badminton in Melaka, welcoming all players.
                To promote active lifestyles and build strong community bonds.
            </div>
            <div class="info-box">
                <strong>Mission</strong><br>
                To provide a safe, inclusive, and affordable space where people of all ages can enjoy badminton, stay active, and connect with others.
            </div>
        </div>
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
