<?php
session_start();
require_once 'db_connection.php';

$name = $email = $phone = '';
$errors = [];

function generateCustomerID($conn) {
    $sql = "SELECT Cust_ID FROM CUSTOMER ORDER BY Cust_ID DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['Cust_ID'];
        $num = intval(substr($lastId, 4)) + 1;
        return "CUST" . str_pad($num, 4, '0', STR_PAD_LEFT);
    } else {
        return "CUST0001";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name)) {
        $errors['name'] = 'Name is required';
    } elseif (strlen($name) > 25) {
        $errors['name'] = 'Name must be 25 characters or less';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    } elseif (strlen($email) > 25) {
        $errors['email'] = 'Email must be 25 characters or less';
    } else {
        $sql = "SELECT Cust_ID FROM CUSTOMER WHERE Cust_Email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors['email'] = 'Email is already taken';
            }
            $stmt->close();
        }
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
        $errors['phone'] = 'Phone must be exactly 11 digits';
    }

    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($errors)) {
        $cust_id = generateCustomerID($conn);

        $sql = "INSERT INTO CUSTOMER (Cust_ID, Cust_Name, Cust_Email, Cust_PhoneNum, Cust_Password) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $cust_id, $name, $email, $phone, $password);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Registration successful! Please login.';
                header("Location: customer-login.php");
                exit();
            } else {
                $errors['database'] = 'Something went wrong. Please try again later.';
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Registration</title>
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
        .left-side { flex: 1; background-color: white; padding: 1.5rem; }
        .right-side { flex: 1; height: 100%; }
        .right-side img { width: 100%; height: 100%; object-fit: cover; }

        .form-group { margin-bottom: 0.7rem; }
        label { font-weight: bold; color: #ff8d8d; display: block; margin-bottom: 0.2rem; font-size: 0.8rem; }
        input[type="email"], input[type="text"], input[type="tel"], input[type="password"] {
            width: 100%; padding: 0.4rem; border: 1.5px solid #ff8d8d;
            border-radius: 5px; background-color: #f9f9f9; font-size: 0.75rem;
        }
        .password-container { position: relative; }
        .toggle-password-btn {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background-color: #ff8d8d; color: white; font-size: 0.7rem;
            border-radius: 5px; text-align: center; width: 50px; padding: 3px 8px;
            user-select: none; cursor: pointer;
        }
        .btn {
            background-color: #ff8d8d; color: white; padding: 0.5rem 1rem; border: none;
            border-radius: 5px; font-weight: bold; transition: background-color 0.3s;
            margin-top: 0.5rem; width: 100%; font-size: 0.75rem;
        }
        .btn:hover { background-color: #e67373; }
        .error-message { color: red; margin-bottom: 0.5rem; font-size: 0.75rem; }
        .register-link { color: #ff8d8d; font-weight: bold; text-align: center; margin-top: 0.8rem; font-size: 0.8rem; }
        .register-link a { color: #ff8d8d; text-decoration: none; transition: 0.3s; }
        .register-link a:hover { color: #ffc0cb; }

.poster-img {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain !important;
    display: block;
}




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
        <h2 style="font-size: 1.3rem; font-weight: 900; margin-bottom: 0.8rem; color: #ff8d8d;">Customer Registration</h2>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required maxlength="25" placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required maxlength="25" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" required pattern="[0-9]{11}" placeholder="11 digit phone number">
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" required minlength="8" placeholder="Create password (min 8 characters)">
                    <div id="togglePassword" class="toggle-password-btn">View</div>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="password-container">
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="8" placeholder="Confirm password (min 8 characters)">
                    <div id="toggleConfirmPassword" class="toggle-password-btn">View</div>
                </div>
            </div>
            <p id="match-error" class="error-message" style="display: none;">Passwords do not match.</p>
            <button type="submit" class="btn">Register</button>
        </form>
        <div class="register-link">
            Already registered? <a href="customer-login.php">Login Now</a>
        </div>
    </div>
        <div class="right-side">
            <img src="customer-registration-poster.png" alt="Registration Poster" class="poster-img">
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
            password.type = 'text';
            togglePassword.textContent = 'Hide';
            isHidden = false;
        } else {
            password.type = 'password';
            togglePassword.textContent = 'View';
            isHidden = true;
        }
    });

    var toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    var confirmPassword = document.getElementById('confirm_password');
    var isConfirmHidden = true;

    toggleConfirmPassword.addEventListener('click', function () {
        if (isConfirmHidden) {
            confirmPassword.type = 'text';
            toggleConfirmPassword.textContent = 'Hide';
            isConfirmHidden = false;
        } else {
            confirmPassword.type = 'password';
            toggleConfirmPassword.textContent = 'View';
            isConfirmHidden = true;
        }
    });

    // Password match validation
    var form = document.querySelector("form");
    var errorMsg = document.getElementById("match-error");

    form.addEventListener("submit", function (e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            errorMsg.style.display = "block";
        } else {
            errorMsg.style.display = "none";
        }
    });

    confirmPassword.addEventListener("input", function () {
        if (password.value === confirmPassword.value) {
            errorMsg.style.display = "none";
        }
    });
</script>


<?php include 'footer.php'; ?>

</body>
</html>

