<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['task_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE task SET Task_Status = ? WHERE Task_ID = ?");
    $stmt->bind_param("si", $status, $taskId);

    if ($stmt->execute()) {
        header("Location: employee-task.php?updated=1");
        exit;
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}
?>