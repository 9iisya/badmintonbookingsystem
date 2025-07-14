<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
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
            color: #333;
            text-align: center;
            padding: 0 2rem;
            flex-direction: column;
        }
        .hero-content {
            max-width: 900px;
        }
        .hero-content h1 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: #06402B;
            font-style: italic;
        }
        .hero-content ul {
            list-style: none;
            padding: 0;
            font-size: 1.2rem;
            line-height: 2;
            text-align: left;
            display: inline-block;
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
            margin: 0.5rem auto;
            display: inline-block;
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
            <li><a href="homepage-about.php">About</a></li>
            <li><a href="homepage-contact.php"class="active">Contact</a></li>
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
        <h1>Contact Us</h1>
        <div style="display: inline-block; text-align: left;">
            <ul>
                <li><strong>Address:</strong> Dewan Badminton PDTJ, Taman Maju, 77000 Jasin, Melaka</li>
                <li><strong>Phone:</strong> +60 6-264 1234</li>
                <li><strong>Email:</strong> pdtjbadminton@gmail.com</li>
                <li><strong>Operating Hours:</strong> 5:00 PM – 11:00 PM, Daily</li>
            </ul>
            <div style="text-align: center; margin-top:">
                <a href="homepage-main-menu.php" class="btn">Back to Home</a>
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
