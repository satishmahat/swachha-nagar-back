<?php
include 'db.php';

// Define variables and initialize with empty values
$ward_number = $section = "";
$schedules = [];

// Fetch schedules based on user selection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ward_number = $_POST['ward_number'];
    $section = $_POST['section'];

    $stmt = $conn->prepare("SELECT * FROM schedules WHERE ward_number = ? AND section = ?");
    $stmt->bind_param("ss", $ward_number, $section);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User View</title>
    <style>
        body {
            font-family: courier, monospace;
            width: 80%;
            margin: auto;
        }

        h1 {
            font-size: 30px;
            margin-top: 50px;
            color: #009600;
        }

        form {
            font-weight: bold;
            font-size: 20px;
            line-height: 30px;
            margin-bottom: 20px;
        }

        select {
            padding: 1px 5px;
            font-size: 16px;
        }

        .submit-button {
            font-size: 16px;
            background-color: #009600;
            cursor: pointer;
            border: 1px solid #009600;
            border-radius: 5px;
            margin-top: 10px;
            padding: 5px 10px;
            color: white;
        }

        .submit-button:hover {
            background-color: white;
            color: #009600;
        }

        /* table */
        table {
            width: 60%;
            border-collapse: collapse;
            margin-bottom:20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #dcf5ee;
        }

        .ward-image {
            display: block;
            margin: 20px 0;
            width: 300px;
            height: 300px;
        }
    </style>
    <script>
        function updateImage() {
            const wardSelect = document.getElementById('ward_number');
            const image = document.getElementById('ward-image');
            const selectedWard = wardSelect.value;
            image.src = selectedWard ? `images/KMC/KMC-${selectedWard}.jpg` : 'images/KMC/KMC.jpg';
        }

        window.onload = function() {
            const selectedWard = "<?php echo $ward_number; ?>";
            if (selectedWard) {
                document.getElementById('ward-image').src = `images/KMC/KMC-${selectedWard}.jpg`;
            }
        }
    </script>
</head>

<body>
    <h1>Help Us Identify Your Location</h1>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="ward_number">Ward Number:</label>
            <select id="ward_number" name="ward_number" onchange="updateImage()" required>
                <option value="">Select Ward</option>
                <option value="14" <?php if ($ward_number == "14") echo "selected"; ?>>14</option>
                <option value="15" <?php if ($ward_number == "15") echo "selected"; ?>>15</option>
                <option value="16" <?php if ($ward_number == "16") echo "selected"; ?>>16</option>
            </select>
            <br>
            <label for="section">Section:</label>
            <select id="section" name="section" required>
                <option value="">Select Section</option>
                <option value="A" <?php if ($section == "A") echo "selected"; ?>>A</option>
                <option value="B" <?php if ($section == "B") echo "selected"; ?>>B</option>
                <option value="C" <?php if ($section == "C") echo "selected"; ?>>C</option>
                <option value="D" <?php if ($section == "D") echo "selected"; ?>>D</option>
                <option value="D" <?php if ($section == "E") echo "selected"; ?>>E</option>
            </select>
            <br>
            <button type="submit" class="submit-button">View Schedule</button>
        </form>

        <img id="ward-image" class="ward-image" src="images/KMC/KMC.jpg" alt="Ward Image">
    </div>

    <?php if (!empty($schedules)) { ?>
        <table>
            <tr>
                <!-- <th>City</th>
                <th>Ward Number</th>
                <th>Section</th> -->
                <th>Type of Waste</th>
                <th>Day</th>
            </tr>
            <?php foreach ($schedules as $schedule) { ?>
                <tr>
                    <!-- <td><?php echo htmlspecialchars($schedule['city']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['ward_number']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['section']); ?></td> -->
                    <td><?php echo htmlspecialchars($schedule['waste_type']); ?></td>
                    <td><?php echo htmlspecialchars($schedule['day']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>

</html>
