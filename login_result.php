<?php
session_start();

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Check if email and password are set
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        echo "<p>Email is found in the database! Now checking password...</p>";
        $row = $result->fetch_assoc();

        if ($row['Password'] == $password) {
            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $row['UserID'];
            $_SESSION['account_type'] = $row['Role'];

            if ($_SESSION['account_type'] == 'seller') {
				// Query the Sellers table to find the SellerID
				$stmt = $connection->prepare("SELECT `SellerID` FROM `Sellers` WHERE `UserID` = ?");
				$stmt->bind_param("s", $_SESSION['username']);
				$stmt->execute();
				$result = $stmt->get_result();
			
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$_SESSION['sellerid'] = $row['SellerID'];
				} else {
					echo "Error - Seller ID not found";
					exit;
				}
			} elseif ($_SESSION['account_type'] == 'buyer') {
				// Query the Buyers table to find the BuyerID
				$stmt = $connection->prepare("SELECT `BuyerID` FROM `Buyer` WHERE `UserID` = ?");
				$stmt->bind_param("s", $_SESSION['username']);
				$stmt->execute();
				$result = $stmt->get_result();
			
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$_SESSION['buyerid'] = $row['BuyerID'];
				} else {
					echo "Error - Buyer ID not found";
					exit;
				}
			}

            echo "<p>Password correct! Yay :) </p>";
            echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
            header("refresh:5;url=index.php");
            $now = new DateTime();
            $_SESSION['login_time'] = $now->format('Y-m-d H:i:s');  
        } else {
            echo "<p>Password incorrect... please enter your credentials again </p>";
            header("refresh:5;url=index.php");
        }
    } else {
        echo "<p>Email not found. Please try again. Redirecting back to home page...</p>";
        header("refresh:5;url=index.php");
    }
} else {
    echo "<p>Please enter both email and password.</p>";
    header("refresh:5;url=index.php");
}
?>