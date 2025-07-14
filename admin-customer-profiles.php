<?php
include 'db_connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['edit'])) {
        $id = $_POST['Cust_ID'];
        $name = $_POST['Cust_Name'];
        $email = $_POST['Cust_Email'];
        $phone = $_POST['Cust_PhoneNum'];

        $sql = "UPDATE CUSTOMER SET Cust_Name='$name', Cust_Email='$email', Cust_PhoneNum='$phone' WHERE Cust_ID='$id'";
        mysqli_query($conn, $sql);
        $message = 'Customer details updated!';
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['Cust_ID'];
        $sql = "DELETE FROM CUSTOMER WHERE Cust_ID='$id'";
        mysqli_query($conn, $sql);
        $message = 'Customer removed!';
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit;
}

if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Profiles</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        html, body { height: 100%; }
        body { display: flex; flex-direction: column; min-height: 100vh; background-color: #f4f4f4; }

        header {
            background-color: #007d14; color: white;
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem 2rem; position: relative;
        }
        .logo { font-size: 1.5rem; font-weight: bold; }
        nav ul { display: flex; list-style: none; gap: 1.5rem; }
        nav ul li a {
            color: white; text-decoration: none; font-weight: bold;
            padding: 8px 16px; border-radius: 5px; transition: 0.3s;
        }
        nav ul li a:hover, nav ul li a.active {
            background-color: white; color: #007d14;
        }
        .hamburger { font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; }
        .dropdown-menu {
            display: none; position: absolute; right: 2rem; top: 70px;
            background-color: #f9f9f9; border-radius: 5px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 160px; z-index: 999;
        }
        .dropdown-menu a {
            display: block; padding: 10px 15px; color: black; text-decoration: none;
        }
        .dropdown-menu a:hover { background-color: #f1f1f1; }
        .show { display: block !important; }

        main { flex: 1; padding: 2rem; }

        .title-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0 1.5rem;
            flex-wrap: wrap;
        }

        .back-button {
            padding: 0.4rem 1rem;
            background-color: #005d10;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: background 0.3s;
            position: relative;
            top: 0px;
        }

        .back-button:hover {
            background-color: #003f0b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007d14;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 5px;
        }

        .button-stack {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .button-stack button {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        button[name="edit"] {
            background-color: #007d14;
            color: white;
        }

        button[name="edit"]:hover {
            background-color: #005d10;
        }

        button[name="delete"] {
            background-color: #cc0000;
            color: white;
        }

        button[name="delete"]:hover {
            background-color: #990000;
        }
    </style>
</head>
<body>

<?php if (!empty($message)): ?>
<script>
    alert("<?= htmlspecialchars($message) ?>");
    if (window.history.replaceState) {
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState(null, null, cleanUrl);
    }
</script>
<?php endif; ?>

<header>
    <div class="logo">ADMIN DASHBOARD</div>
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
    <div class="title-bar">
        <a href="admin-customer-menu.php" class="back-button">Back</a>
        <h2>Customer Profiles</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM CUSTOMER");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <form method='POST'>
                        <td><input type='text' name='Cust_ID' value='{$row['Cust_ID']}' readonly></td>
                        <td><input type='text' name='Cust_Name' value='{$row['Cust_Name']}'></td>
                        <td><input type='email' name='Cust_Email' value='{$row['Cust_Email']}'></td>
                        <td><input type='text' name='Cust_PhoneNum' value='{$row['Cust_PhoneNum']}'></td>
                        <td class='button-stack'>
                            <button type='submit' name='edit'>Edit</button>
                            <button type='submit' name='delete' onclick=\"return confirm('Delete this customer?')\">Delete</button>
                        </td>
                    </form>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</main>

<script>
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('show');
    }
</script>
<?php include 'footer.php'; ?>

</body>
</html>
