<?php
include 'db.php';
session_start(); // Ensure session handling is started

// Logout logic
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to the login page
    exit();
}

// Initialize variables
$status = $message = "";

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submissions for adding, updating, and deleting schedules
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action == 'add') {
        // Add new schedule
        $city = $_POST['city'];
        $ward_number = $_POST['ward_number'];
        $section = $_POST['section'];
        $waste_type = $_POST['waste_type'];
        $day = $_POST['day'];

        $stmt = $conn->prepare("INSERT INTO schedules (city, ward_number, section, waste_type, day) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $city, $ward_number, $section, $waste_type, $day);
            if ($stmt->execute()) {
                $status = 'success';
                $message = 'Schedule added successfully.';
            } else {
                $status = 'error';
                $message = 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $status = 'error';
            $message = 'Prepare failed: ' . $conn->error;
        }
    } elseif ($action == 'delete') {
        // Delete schedule
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $status = 'success';
                $message = 'Schedule deleted successfully.';
            } else {
                $status = 'error';
                $message = 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $status = 'error';
            $message = 'Prepare failed: ' . $conn->error;
        }
    } elseif ($action == 'edit') {
        // Edit schedule
        $id = $_POST['id'];
        $city = $_POST['city'];
        $ward_number = $_POST['ward_number'];
        $section = $_POST['section'];
        $waste_type = $_POST['waste_type'];
        $day = $_POST['day'];

        $stmt = $conn->prepare("UPDATE schedules SET city = ?, ward_number = ?, section = ?, waste_type = ?, day = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("sssssi", $city, $ward_number, $section, $waste_type, $day, $id);
            if ($stmt->execute()) {
                $status = 'success';
                $message = 'Schedule updated successfully.';
            } else {
                $status = 'error';
                $message = 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $status = 'error';
            $message = 'Prepare failed: ' . $conn->error;
        }
    }
}

// Fetch all schedules
$sql = "SELECT * FROM schedules";
$result = $conn->query($sql);

if (!$result) {
    die('Query failed: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules</title>
    <style>
    body {
        font-family: courier, monospace;
        width:80%;
        margin:auto;
    }
    body h1{
        color:#009600;
        margin-top:50px;
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
        background-color: #dcf5ee;
    }

    .form-container {
        margin-bottom: 20px;
        display: none;
    }

    .form-container form {
        margin: 0;
    }

    .form-container input,
    .form-container select {
        margin-bottom: 10px;
        display: block;
        width: 100%;
        box-sizing: border-box;
    }

    .form-container button {
        display: inline-block;
        margin-top: 10px;
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

    .edit-mode td {
        cursor: text;
        background-color: #f9f9f9;
    }

    .waste-type-select,
    .ward-number-select,
    .section-select,
    .day-select {
        width: 100%;
        box-sizing: border-box;
    }

    .hidden {
        display: none;
    }
    .submit-button{
        font-size: 16px;
        background-color:#009600;
        color:white;
        cursor: pointer;
        border: 1px solid #009600;
        border-radius: 5px;
        margin-top: 10px; 
        padding: 5px 10px;
    }

    .submit-button:hover{
        background-color: white;
        color: black;
    }
    .insert-button,
    .logout-button {
        text-decoration:none;
        margin: 20px 0 20px 0;
        padding: 5px 20px;
        font-size: 18px;
        cursor: pointer;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        font-family: courier, monospace;

    }
    .insert-button:hover,
    .logout-button:hover {
        background-color: #45a049;
    }


    label{
        font-weight:bold;
    }
    input{
        margin-top:5px;
        max-width:500px;
        padding:2px 10px;
        border-radius:5px;
    }
    select{
        margin-top:5px;
        max-width:500px;
        border-radius:5px;
        padding:2px 10px;
    }
    </style>
    <script>
    function toggleEditMode(row) {
        var isEditing = row.classList.toggle('edit-mode');
        row.querySelectorAll('.editable').forEach(cell => {
            cell.contentEditable = isEditing;
            if (isEditing) {
                cell.focus();
            }
        });
        row.querySelectorAll('.dropdown-select').forEach(select => {
            select.classList.toggle('hidden');
        });
        row.querySelector('.save-button').style.display = isEditing ? 'inline' : 'none';
        row.querySelectorAll('.display-value').forEach(span => {
            span.style.display = isEditing ? 'none' : 'inline';
        });
    }

    function saveChanges(id) {
        var row = document.getElementById('row-' + id);
        var city = row.querySelector('.editable[data-field="city"]').innerText;
        var wardNumber = row.querySelector('.ward-number-select').value;
        var section = row.querySelector('.section-select').value;
        var wasteType = row.querySelector('.waste-type-select').value;
        var day = row.querySelector('.day-select').value;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        var actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'edit';
        form.appendChild(actionInput);

        var idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);

        var cityInput = document.createElement('input');
        cityInput.type = 'hidden';
        cityInput.name = 'city';
        cityInput.value = city;
        form.appendChild(cityInput);

        var wardNumberInput = document.createElement('input');
        wardNumberInput.type = 'hidden';
        wardNumberInput.name = 'ward_number';
        wardNumberInput.value = wardNumber;
        form.appendChild(wardNumberInput);

        var sectionInput = document.createElement('input');
        sectionInput.type = 'hidden';
        sectionInput.name = 'section';
        sectionInput.value = section;
        form.appendChild(sectionInput);

        var wasteTypeInput = document.createElement('input');
        wasteTypeInput.type = 'hidden';
        wasteTypeInput.name = 'waste_type';
        wasteTypeInput.value = wasteType;
        form.appendChild(wasteTypeInput);

        var dayInput = document.createElement('input');
        dayInput.type = 'hidden';
        dayInput.name = 'day';
        dayInput.value = day;
        form.appendChild(dayInput);

        document.body.appendChild(form);
        form.submit();
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this schedule?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            var idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            form.appendChild(idInput);

            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        }
    }

    function toggleForm() {
        var formContainer = document.querySelector('.form-container');
        formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
    }
    </script>
</head>

<body>
    <h1>Manage Garbage Truck Schedules</h1>

    <button class="insert-button" onclick="toggleForm()">Insert New Schedule</button>
    <a href="?action=logout" class="logout-button">Logout</a>

    <div class="form-container">
        <h2>Add New Schedule</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="Kathmandu" required>
            <br>
            <label for="ward_number">Ward Number:</label>
            <select id="ward_number" name="ward_number" required>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
            </select>
            <br>
            <label for="section">Section:</label>
            <select id="section" name="section" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
            </select>
            <br>
            <label for="waste_type">Type of Waste:</label>
            <select id="waste_type" name="waste_type" required>
                <option value="Decomposable">Decomposable</option>
                <option value="Non-decomposable">Non-decomposable</option>
            </select>
            <br>
            <label for="day">Day:</label>
            <select id="day" name="day" required>
                <option value="Sunday">Sunday</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
            </select>
            <br>
            <input type="hidden" name="action" value="add">
            <button type="submit" class="submit-button">Add Schedule</button>
        </form>
    </div>

    <?php if ($status) { ?>
    <div class="message <?php echo $status; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php } ?>

    <h2>Existing Schedules</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>City</th>
            <th>Ward Number</th>
            <th>Section</th>
            <th>Type of Waste</th>
            <th>Day</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr id="row-<?php echo $row['id']; ?>">
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td class="editable" data-field="city"><?php echo htmlspecialchars($row['city']); ?></td>
            <td>
                <span class="display-value"><?php echo htmlspecialchars($row['ward_number']); ?></span>
                <select class="dropdown-select ward-number-select hidden">
                    <option value="14" <?php if ($row['ward_number'] == '14') echo 'selected'; ?>>14</option>
                    <option value="15" <?php if ($row['ward_number'] == '15') echo 'selected'; ?>>15</option>
                    <option value="16" <?php if ($row['ward_number'] == '16') echo 'selected'; ?>>16</option>
                </select>
            </td>
            <td>
                <span class="display-value"><?php echo htmlspecialchars($row['section']); ?></span>
                <select class="dropdown-select section-select hidden">
                    <option value="A" <?php if ($row['section'] == 'A') echo 'selected'; ?>>A</option>
                    <option value="B" <?php if ($row['section'] == 'B') echo 'selected'; ?>>B</option>
                    <option value="C" <?php if ($row['section'] == 'C') echo 'selected'; ?>>C</option>
                    <option value="D" <?php if ($row['section'] == 'D') echo 'selected'; ?>>D</option>
                    <option value="D" <?php if ($row['section'] == 'E') echo 'selected'; ?>>E</option>
                </select>
            </td>
            <td>
                <span class="display-value"><?php echo htmlspecialchars($row['waste_type']); ?></span>
                <select class="dropdown-select waste-type-select hidden">
                    <option value="Decomposable" <?php if ($row['waste_type'] == 'Decomposable') echo 'selected'; ?>>
                        Decomposable</option>
                    <option value="Non-decomposable"
                        <?php if ($row['waste_type'] == 'Non-decomposable') echo 'selected'; ?>>Non-decomposable
                    </option>
                </select>
            </td>
            <td>
                <span class="display-value"><?php echo htmlspecialchars($row['day']); ?></span>
                <select class="dropdown-select day-select hidden">
                    <option value="Sunday" <?php if ($row['day'] == 'Sunday') echo 'selected'; ?>>Sunday</option>
                    <option value="Monday" <?php if ($row['day'] == 'Monday') echo 'selected'; ?>>Monday</option>
                    <option value="Tuesday" <?php if ($row['day'] == 'Tuesday') echo 'selected'; ?>>Tuesday</option>
                    <option value="Wednesday" <?php if ($row['day'] == 'Wednesday') echo 'selected'; ?>>Wednesday
                    </option>
                    <option value="Thursday" <?php if ($row['day'] == 'Thursday') echo 'selected'; ?>>Thursday</option>
                    <option value="Friday" <?php if ($row['day'] == 'Friday') echo 'selected'; ?>>Friday</option>
                    <option value="Saturday" <?php if ($row['day'] == 'Saturday') echo 'selected'; ?>>Saturday</option>
                </select>
            </td>
            <td>
                <button type="button" onclick="toggleEditMode(this.closest('tr'));">Edit</button>
                <button type="button" class="save-button hidden"
                    onclick="saveChanges(<?php echo $row['id']; ?>);">Save</button>
                <button type="button" onclick="confirmDelete(<?php echo $row['id']; ?>);">Delete</button>
            </td>
        </tr>
        <?php } ?>
    </table>

</body>

</html>
<?php
$conn->close();
?>