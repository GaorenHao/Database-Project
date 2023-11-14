<?php
// db_connect.php
$server = 'localhost';
$username = 'root';
$password = 'root';
$database = 'auction';

$connection = mysqli_connect($server, $username, $password, $database);

if ($connection){
    echo "Connection Successful!";
} else{
  die(mysqli_error($connection));
}