<?php
$servername = "localhost";
$username = "root";
$password = ""; // Update with your password if any
$dbname = "test"; // Update with your database name if needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "MySQLi extension is installed and connection successful.";
$conn->close();
?>
