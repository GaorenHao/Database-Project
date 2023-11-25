<?php
include_once("header.php");
include 'db_connect.php';
session_start();

$userID = $_SESSION['username']; // get user id



// update user table
$logout_query = "UPDATE Users SET LastLogout = NOW() WHERE UserID = $userID";

$logout_result = $connection->query($logout_query);

if ($connection->query($logout_query) === TRUE) {

    unset($_SESSION['logged_in']);
    unset($_SESSION['account_type']);
    setcookie(session_name(), "", time() - 360);
    session_destroy();

    echo "Successfully logged out & tracked log out time";

    //Redirect to index
    header("Location: index.php");

} else {
    echo "Error updating logout time & logging out ";}

    //header("Location: index.php");
?>