<?php
include 'db.php'; // Include the db.php file

if ($conn) {
    echo 'Database connection successful!';
} else {
    echo 'Database connection failed.';
}

mysqli_close($conn); // Close the connection
?>