<?php
// db_connect.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "UIH";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

#echo "Connected successfully";
?>
