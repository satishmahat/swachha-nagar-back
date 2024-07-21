<?php
include 'db.php';

// Fetch all schedules
$sql = "SELECT * FROM schedules";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Schedules</title>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
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
        background-color: #f2f2f2;
    }
    </style>
</head>

<body>
    <h1>Garbage Truck Schedules</h1>
    <table>
        <tr>

            <th>City</th>
            <th>Ward Number</th>
            <th>Section</th>
            <th>Type of Waste</th>

        </tr>
        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>

            <td><?php echo htmlspecialchars($row['city']); ?></td>
            <td><?php echo htmlspecialchars($row['ward_number']); ?></td>
            <td><?php echo htmlspecialchars($row['section']); ?></td>
            <td><?php echo htmlspecialchars($row['waste_type']); ?></td>

        </tr>
        <?php } ?>
    </table>
</body>

</html>