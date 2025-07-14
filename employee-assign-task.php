<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskName = $_POST['task_name'];
    $employeeId = $_POST['employee_id']; // should be a string like 'EMP001'

    $stmt = $conn->prepare("INSERT INTO task (Task_Name, Task_Assigned_To, Task_Status) VALUES (?, ?, 'Pending')");
    $stmt->bind_param("ss", $taskName, $employeeId); // both are strings

    if ($stmt->execute()) {
        header("Location: admin-employee-logs.php?success=1");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
