<?php
include 'db.php'; // Include the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $city = $_POST['city'];
    $ward_number = $_POST['ward_number'];
    $section = $_POST['section'];
    $waste_type = $_POST['waste_type'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("UPDATE schedules SET city = ?, ward_number = ?, section = ?, waste_type = ?, date = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sssssi", $city, $ward_number, $section, $waste_type, $date, $id);
        if ($stmt->execute()) {
            echo 'Update successful';
        } else {
            echo 'Error: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'Prepare failed: ' . $conn->error;
    }

    $conn->close();
}
?>