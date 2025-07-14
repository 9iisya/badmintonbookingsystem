<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: white;
        }
        header {
            background-color: #007d14;
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
            color: #007d14;
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

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem 0; 
        }

        img {
            height: 300px; 
        }

        main > div {
            gap: 2rem; 
        }

        .custom-btn {
            width: 220px; 
            padding: 0.6rem 1rem;
            background-color: #007d14;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
            font-size: 1rem;
            display: inline-block;
            text-align: center;
        }
        .custom-btn:hover {
            background-color: #2e7d32;
        }
        .icon {
            width: 40px;
            height: 40px;
        }

        .show { display: block !important; }
    </style>
</head>
<body>

<header>
    <div class="logo">
        ADMIN DASHBOARD
    </div>
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
    <div style="display: flex; justify-content: center; align-items: center; gap: 4rem;">
        <div>
            <img src="admin-dashboard.png" alt="Badminton Player" style="height: 400px;">
        </div>
        <div style="display: flex; flex-direction: column; gap: 2rem; align-items: center;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="admin-customer-profiles.php" class="custom-btn">CUSTOMER PROFILES</a>
                <img src="admin-dashboard-employee-profiles.png" alt="Customer Profiles" class="icon">
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="admin-customer-bookings.php" class="custom-btn">BOOKING HISTORY</a>
                <img src="admin-customer-menu-booking-history.png" alt="Booking History" class="icon">
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="admin-customer-feedback.php" class="custom-btn">FEEDBACKS</a>
                <img src="admin-customer-menu-feedback.png" alt="Feedbacks" class="icon">
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }
</script>

</body>
</html>
