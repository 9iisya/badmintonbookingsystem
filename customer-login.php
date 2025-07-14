<?php
session_start();
require_once 'db_connection.php';

$email = '';
$error = '';

// Redirect if already logged in
if (isset($_SESSION['cust_id'])) {
    header("Location: customer-booking-schedule.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $sql = "SELECT Cust_ID, Cust_Name, Cust_Email, Cust_Password FROM CUSTOMER WHERE Cust_Email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $name, $emailDb, $passwordDb);
                    $stmt->fetch();

                    if ($password === $passwordDb) {
                        $_SESSION['cust_id'] = $id;
                        $_SESSION['cust_name'] = $name;
                        $_SESSION['cust_email'] = $emailDb;
                        header("Location: customer-booking-schedule.php");
                        exit();
                    } else {
                        $error = "Invalid email or password.";
                    }
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: white; }
        header {
            background-color: #ff8d8d; color: white;
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 2rem; position: relative;
        }
        .logo { display: flex; align-items: center; font-size: 1.5rem; font-weight: bold; }
        .logo img { height: 30px; margin-left: 10px; }
        nav ul { display: flex; list-style: none; gap: 1.5rem; }
        nav ul li a {
            color: white; text-decoration: none; font-weight: bold; padding: 8px 16px;
            border-radius: 5px; transition: 0.3s;
        }
        nav ul li a:hover, nav ul li a.active {
            background-color: white; color: #ff8d8d;
        }
        .hamburger { font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; }
        .dropdown-menu {
            display: none; position: absolute; right: 2rem; top: 70px;
            background-color: #f9f9f9; border-radius: 5px; box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            min-width: 160px; z-index: 100;
        }
        .dropdown-menu a {
            display: block; padding: 10px 15px; color: black; text-decoration: none;
        }
        .dropdown-menu a:hover { background-color: #f1f1f1; }
        .show { display: block !important; }

        .hero { display: flex; width: 100%; height: 78vh; }
        .left-side { flex: 1; background-color: white; padding: 2rem; }
        .right-side { flex: 1; height: 100%; }
        .right-side img { width: 100%; height: 100%; object-fit: cover; }
        .form-group { margin-bottom: 1rem; }
        label { font-weight: bold; color: #ff8d8d; display: block; margin-bottom: 0.3rem; }
        input[type="email"], input[type="text"] {
            width: 100%; padding: 0.6rem; border: 1.5px solid #ff8d8d;
            border-radius: 5px; background-color: #f9f9f9; font-size: 0.85rem;
        }
        input[type="text"] { padding-right: 80px; -webkit-text-security: disc; }
        .password-container { position: relative; }
        .toggle-password-btn {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background-color: #ff8d8d; color: white; font-size: 0.8rem;
            border-radius: 5px; text-align: center; width: 60px; padding: 5px 10px;
            user-select: none; cursor: pointer;
        }
        .btn {
            background-color: #ff8d8d; color: white; padding: 0.6rem 1.1rem; border: none;
            border-radius: 5px; font-weight: bold; transition: background-color 0.3s;
            margin-top: 1rem; width: 100%; font-size: 0.85rem;
        }
        .btn:hover { background-color: #e67373; }
        .error-message { color: red; margin-bottom: 0.8rem; text-align: center; }
        .register-link { color: #ff8d8d; font-weight: bold; text-align: center; margin-top: 1rem; }
        .register-link a { color: #ff8d8d; text-decoration: none; transition: 0.3s; }
        .register-link a:hover { color: #ffc0cb; }
    </style>
</head>
<body>

<header>
    <div class="logo">BADMINTON <img src="homepage-main-menu-shuttlecock-symbol.png" alt="Logo"></div>
    <nav>
        <ul>
            <li><a href="homepage-main-menu.php">Home</a></li>
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
    <div class="left-side">
        <h2 style="font-size: 1.5rem; font-weight: 900; margin-bottom: 1rem; color: #ff8d8d;">Welcome to Customer Login</h2>
        <?php if (!empty($error)) { ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-container">
                    <input type="text" name="password" id="password" required placeholder="Enter your password">
                    <div id="togglePassword" class="toggle-password-btn">View</div>
                </div>
            </div>
            <button type="submit" class="btn">Log In</button>
        </form>
        <div class="register-link">
            New user? <a href="customer-registration.php">Register Now</a>
        </div>
    </div>
    <div class="right-side">
        <img src="management-login-poster.png" alt="Login Poster">
    </div>
</section>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }

    var togglePassword = document.getElementById('togglePassword');
    var password = document.getElementById('password');
    var isHidden = true;

    togglePassword.addEventListener('click', function () {
        if (isHidden) {
            password.style.webkitTextSecurity = 'none';
            togglePassword.textContent = 'Hide';
        } else {
            password.style.webkitTextSecurity = 'disc';
            togglePassword.textContent = 'View';
        }
        isHidden = !isHidden;
    });
</script>

<?php include 'footer.php'; ?>

</body>
</html>
