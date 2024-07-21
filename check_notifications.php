<?php
include 'db.php';

$current_time = date('Y-m-d H:i:s');

// Select schedules where the truck has left and has not been notified
$sql = "SELECT * FROM schedules WHERE date < ? AND notified = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_time);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;

    // Mark as notified
    $update_stmt = $conn->prepare("UPDATE schedules SET notified = 1 WHERE id = ?");
    $update_stmt->bind_param("i", $row['id']);
    $update_stmt->execute();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($notifications);
?>