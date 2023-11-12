<?php


// Create connection
<?php
$servername = "localhost"; // or your server name
$username = "your_username";
$password = "your_password";
$dbname = "auction";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check connection
