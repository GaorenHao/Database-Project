<?php
session_start(); // Start the session
ob_start(); // Start output buffering
include_once("header.php");
include 'db_connect.php'; // Include database connection

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $userID = $_SESSION['username']; // Get user id from the session

    // Prepare and execute logout query
    $logout_query = "UPDATE Users SET LastLogout = NOW() WHERE UserID = ?";
    $stmt = $connection->prepare($logout_query);
    $stmt->bind_param("i", $userID); // Assuming userID is an integer, use "s" if it's a string

    if ($stmt->execute()) {
        // Clear session data and destroy the session
        $_SESSION = array();
        setcookie(session_name(), "", time() - 3600, "/");
        session_destroy();

        // Redirect to index.php
        header("Location: index.php");
        exit(); // Ensure no further execution
    } else {
        echo "Error updating logout time: " . $stmt->error;
    }
} else {
    echo "Not logged in or invalid session.";
}

ob_end_flush(); // Send output buffer and turn off output buffering
?>
