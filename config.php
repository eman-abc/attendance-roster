<?php
$servername = "localhost"; // Your MySQL server
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (default is empty)
$dbname = "roster";    // Database name (create it if not done already)

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>