 <?php
// db_connect.php
$server = 'localhost';
$username = 'root';
$password = 'root';
$database = 'Auction';

$connection = mysqli_connect($server, $username, $password, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optionally, you can set the connection to use exceptions for error handling if you're using MySQLi in an OOP context
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
