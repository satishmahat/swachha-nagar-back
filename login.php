<?php
session_start(); // Start a new session or resume the existing one

// Define hard-coded username and password (for demonstration purposes)
$valid_username = 'admin';
$valid_password = 'password'; // Change this in production!

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check credentials
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true; // Set session variable to indicate login status
        header("Location: manage_schedule.php"); // Redirect to the edit page (correct URL)
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
    body {
        font-family: courier;
        height: 100vh;
        background-color: #f4f4f4;
        /* margin: auto; */
    }
    body h1{
        margin: 50px 0 20px 0;
        font-size:40px;
        color: #009600;
    }
    form {
        display: flex;
        flex-direction:column;
        justify-content: center;
        margin: 0 auto;
        width: 300px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    label{
        font-weight:bold;
    }
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #009600;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #45a049;
    }

    .error {
        color: red;
        margin-top: 10px;
    }
    </style>
</head>

<body>
    <h1 style="text-align: center;">Enter Your Given Admin Details</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>

    <?php
    if (isset($error)) {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    ?>
</body>

</html>