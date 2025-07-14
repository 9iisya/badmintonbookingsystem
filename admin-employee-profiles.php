<?php
// Connect to database
include 'db_connection.php';

$message = '';

// Handle add, edit, delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $id = $_POST['Employee_ID'];
        $name = $_POST['Emp_Name'];
        $phone = $_POST['Emp_PhoneNum'];
        $email = $_POST['Emp_Email'];
        $password = $_POST['Emp_Password'];
        $admin = 'AD01'; // Fixed Admin ID

        // Handle image upload
        $image = $_FILES['Emp_Image']['name'];
        $tmp_name = $_FILES['Emp_Image']['tmp_name'];
        $imagePath = '';

        if (!empty($image)) {
            if (!is_dir('uploads')) {
                mkdir('uploads');
            }
            $imagePath = 'uploads/' . basename($image);
            move_uploaded_file($tmp_name, $imagePath);
        }

        $sql = "INSERT INTO EMPLOYEE (Employee_ID, Emp_Name, Emp_PhoneNum, Emp_Email, Emp_Password, Emp_Image, Admin_ID) 
                VALUES ('$id', '$name', '$phone', '$email', '$password', '$image', '$admin')";
        mysqli_query($conn, $sql);
        $message = 'New employee added!';
    }

    if (isset($_POST['edit'])) {
        $id = $_POST['Employee_ID'];
        $name = $_POST['Emp_Name'];
        $phone = $_POST['Emp_PhoneNum'];
        $email = $_POST['Emp_Email'];
        $password = $_POST['Emp_Password'];
        $admin = 'AD01'; // Fixed Admin ID

        $image = $_FILES['Emp_Image']['name'];
        $tmp_name = $_FILES['Emp_Image']['tmp_name'];

        if (!empty($image)) {
            $imagePath = 'uploads/' . basename($image);
            move_uploaded_file($tmp_name, $imagePath);
            $sql = "UPDATE EMPLOYEE SET Emp_Name='$name', Emp_PhoneNum='$phone', Emp_Email='$email', Emp_Password='$password', Admin_ID='$admin', Emp_Image='$image' WHERE Employee_ID='$id'";
        } else {
            $sql = "UPDATE EMPLOYEE SET Emp_Name='$name', Emp_PhoneNum='$phone', Emp_Email='$email', Emp_Password='$password', Admin_ID='$admin' WHERE Employee_ID='$id'";
        }

        mysqli_query($conn, $sql);
        $message = 'Employee details updated!';
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['Employee_ID'];
        $sql = "DELETE FROM EMPLOYEE WHERE Employee_ID='$id'";
        mysqli_query($conn, $sql);
        $message = 'Employee details removed!';
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
    <title>Employee Profiles</title>
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
    h2 { text-align: center; margin-bottom: 1.5rem; color: #007d14; }

    .form-container {
        max-width: 100%;
        margin: 0 auto 2rem auto;
        background-color: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        justify-content: center;
    }
    .form-container form {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 10px;
        flex-direction: row;
        width: 100%;
        justify-content: center;
        flex-shrink: 0;
        overflow-x: auto;
    }
    .form-container h3 {
        margin-bottom: 1rem;
        text-align: center;
        color: #007d14;
        width: 100%;
    }
    .form-container input[type="text"],
    .form-container input[type="email"],
    .form-container input[type="file"] {
        width: 150px;
        padding: 0.4rem;
        font-size: 14px;
    }
    .form-container button {
        padding: 0.4rem 1rem;
        font-size: 14px;
        background: #007d14;
        color: white;
        border: none;
    }
    .form-container button:hover {
        background: #005d10;
    }

    table {
        width: 100%; border-collapse: collapse; margin: 0 auto 2rem auto;
        background-color: white; border-radius: 8px; overflow: hidden;
    }
    th, td {
        padding: 0.5rem; text-align: left; border: 1px solid #ddd;
    }
    th { background-color: #007d14; color: white; }
    tr:nth-child(even) { background-color: #f1f1f1; }
    td:first-child, th:first-child {
        max-width: 100px;
        width: 100px;
        white-space: nowrap;
    }
    td button,
    .form-container button {
        padding: 0.4rem 1rem;
        font-size: 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }
    td button[name="edit"] {
        background-color: #007d14;
        color: white;
        margin-right: 5px;
    }
    td button[name="edit"]:hover {
        background-color: #005d10;
    }
    td button[name="delete"] {
        background-color: #cc0000;
        color: white;
    }
    td button[name="delete"]:hover {
        background-color: #990000;
    }
    input[type="file"] {
        white-space: pre-line;
        font-size: 10px;
    }
    img.profile-pic {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        display: block;
        margin: 0 auto 0.5rem auto;
    }
    .button-stack {
        display: flex;
        flex-direction: column;
        gap: 6px;
        align-items: stretch;
    }
    .button-stack button {
        width: 100%;
    }
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
        top: -12px;
    }
    .back-button:hover {
        background-color: #003f0b;
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
            <li><a href="admin-employee-menu.php" class="active">EMPLOYEE</a></li>
            <li><a href="admin-customer-menu.php">CUSTOMER</a></li>
            <li><a href="admin-report-menu.php">REPORT</a></li>
        </ul>
    </nav>
    <div class="hamburger" onclick="toggleDropdown()">&#9776;</div>
    <div class="dropdown-menu" id="dropdownMenu">
        <a href="logout.php">Logout</a>
    </div>
</header>

<div class="title-bar">
    <a href="admin-employee-menu.php" class="back-button">Back</a>
    <h2>Employee Profiles</h2>
</div>

<!-- Employee Table -->
<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email & Password</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM EMPLOYEE");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                <form method='POST' enctype='multipart/form-data'>
                    <td>
                        " . (!empty($row['Emp_Image']) ? "<img src='uploads/{$row['Emp_Image']}' class='profile-pic'><br>" : "No Image<br>") . "
                        <input type='file' name='Emp_Image'>
                    </td>
                    <td><input type='text' name='Employee_ID' value='{$row['Employee_ID']}' readonly></td>
                    <td><input type='text' name='Emp_Name' value='{$row['Emp_Name']}'></td>
                    <td><input type='text' name='Emp_PhoneNum' value='{$row['Emp_PhoneNum']}'></td>
                    <td>
                        <input type='email' name='Emp_Email' value='{$row['Emp_Email']}' placeholder='Email' style='margin-bottom:4px;'><br>
                        <input type='text' name='Emp_Password' value='{$row['Emp_Password']}' placeholder='Password'>
                    </td>
                    <td>
                        <div class='button-stack'>
                            <button type='submit' name='edit'>Edit</button>
                            <button type='submit' name='delete' onclick=\"return confirm('Delete this employee?')\">Delete</button>
                        </div>
                    </td>
                </form>
            </tr>";
        }
        ?>
    </tbody>
</table>

<!-- Add Form -->
<div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <h3>Add New Employee</h3>
        <input type="text" name="Employee_ID" placeholder="Employee ID" required>
        <input type="text" name="Emp_Name" placeholder="Name" required>
        <input type="text" name="Emp_PhoneNum" placeholder="Phone Number" required>
        <div style="display: flex; flex-direction: column; align-items: center;">
            <input type="email" name="Emp_Email" placeholder="Email" required style="margin-bottom: 4px;">
            <input type="text" name="Emp_Password" placeholder="Password" required>
        </div>
        <input type="file" name="Emp_Image" accept="image/*">
        <button type="submit" name="add">Add</button>
    </form>
</div>

<script>
function toggleDropdown() {
    document.getElementById('dropdownMenu').classList.toggle('show');
}
</script>
<?php include 'footer.php'; ?>

</body>
</html>
