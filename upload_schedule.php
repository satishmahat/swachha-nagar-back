<?php
include 'db.php'; // Include the database connection file

// Define variables and initialize with empty values
$city = $ward_number = $section = $waste_type = $date = "";
$status = $error = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $city = $_POST['city'];
    $ward_number = $_POST['ward_number'];
    $section = $_POST['section'];
    $waste_type = $_POST['waste_type'];
    $date = $_POST['date'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO schedules (city, ward_number, section, waste_type, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $city, $ward_number, $section, $waste_type, $date);

    // Execute statement
    if ($stmt->execute()) {
        $status = 'success';
    } else {
        $status = 'error';
        $error = $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Schedule</title>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    form {
        margin-bottom: 20px;
    }

    .message {
        font-size: 1.2em;
        font-weight: bold;
    }

    .success {
        color: green;
    }

    .error {
        color: red;
    }
    </style>
</head>

<body>
    <h1>Upload Garbage Truck Schedule</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="city">City:</label>
        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
        <br>
        <label for="ward_number">Ward Number:</label>
        <input type="text" id="ward_number" name="ward_number" value="<?php echo htmlspecialchars($ward_number); ?>"
            required>
        <br>
        <label for="section">Section:</label>
        <input type="text" id="section" name="section" value="<?php echo htmlspecialchars($section); ?>" required>
        <br>
        <label for="waste_type">Type of Waste:</label>
        <select id="waste_type" name="waste_type" required>
            <option value="Decomposable" <?php if ($waste_type == 'Decomposable') echo 'selected'; ?>>Decomposable
            </option>
            <option value="Non-decomposable" <?php if ($waste_type == 'Non-decomposable') echo 'selected'; ?>>
                Non-decomposable</option>
        </select>
        <br>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
        <br>
        <button type="submit">Upload Schedule</button>
    </form>

    <?php
    if ($status == 'success') {
        echo '<div class="message success">Info has been updated to the database. Complete.</div>';
    } elseif ($status == 'error') {
        echo '<div class="message error">Error: ' . htmlspecialchars($error) . '</div>';
    }
    ?>
</body>

</html>