<?php
$server = 'localhost';
$port = '3306'; // Update if you use a custom port
$userName = 'root';
$password = '';
$dbName = 'SwachhaNagar';

// Create connection
$conn = mysqli_connect($server, $userName, $password, $dbName, $port);

// Check connection
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>