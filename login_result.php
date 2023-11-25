<?php
session_start();

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';


// TODO: Extract $_POST variables, check they're OK, and attempt to login.
//var_dump($_POST);
$email = $_POST['email'];
$password = $_POST['password'];

//// Jess: so we need get the user inputs and compare to what is in the SQL table for user. 
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $connection->query($sql);
// if there are no rows in the result object, then it means the email is not registered (so invalid email entered)
if ($result->num_rows > 0) {
    echo "<p>Email is found in the database! Now checking password...</p>";
	//$allRows = $result->fetch_all(MYSQLI_ASSOC);
    //print_r($allRows); // Print all rows at once
	$row = $result->fetch_assoc();
	//print_r($row);
	//print_r($row['Password']);
	//echo $password;
	if ($row['Password'] == $password) {
		
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
				//print_r($row);
				$_SESSION['sellerid'] = $row['SellerID'];
				//echo $_SESSION['sellerid'];
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
				//echo $_SESSION['buyerid'];
			} else {
				
				echo "Error - Buyer ID not found";
				exit;
			}
		}

		echo "<p>Password correct! Yay :) </p>";
		echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
//echo $_SESSION['username'];
//echo $_SESSION['account_type'];
//echo $_SESSION['login_time'];

// Redirect to index after 5 seconds
		header("refresh:5;url=index.php");
		// only at this point, has the user actually signed in. so we store a timestamp of when they have logged in 
		$now = new DateTime();
		$_SESSION['login_time'] = $now->format('Y-m-d H:i:s');  // the login time should now be available to access across the web app 
	} else {
		// Notify user of success/failure and redirect/give navigation options.
		echo "<p>Password incorrect... please enter your credentials again </p>";
		header("refresh:5;url=index.php");
	}
  } else {
    //echo "<p>Email is not found. Need to send user to try again</p>";
	header("refresh:5;url=index.php");
  }


// Could do with ... ^ dealing with results where the user is both a buyer and seller. but ignore this and leave for later ... 

// For now, I will just set session variables and redirect.

// Jess: what are session variables ?? 



//echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
//echo $_SESSION['username'];
//echo $_SESSION['account_type'];
//echo $_SESSION['login_time'];

// Redirect to index after 5 seconds
//header("refresh:5;url=index.php");

// now need some way of ... utilising this user information that we have gathered, to modify the browser view.

?>