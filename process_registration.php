<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include 'db_connect.php';

// Now you can use $connection to interact with the database

//var_dump($_POST);
$accountType = $_POST['accountType'];
$email = $_POST['email'];
$password = $_POST['password'];
$passwordConfirmation = $_POST['passwordConfirmation'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];

// Assign default values if first name and last name are not provided
//$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : 'N/A';
//$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : 'N/A';

$stmt = $connection->prepare("INSERT INTO users (role, email, password, firstName, lastName) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $accountType, $email, $password, $firstName, $lastName);

// Execute the prepared statement
//$stmt->execute();

if ($password !== $passwordConfirmation) {
  echo "Passwords do not match, please try again! You are being redirected beack to the homepage.".$stmt->error;
  header("refresh:3;url=index.php");
  exit;
}
$stmt->execute();
if ($stmt->execute()) {
  
  
  $recent_userID = $connection -> insert_id;
  //$print = "";
  if ($accountType === 'seller') {
    
    $stmt = $connection->prepare("INSERT INTO sellers (UserID) VALUES (?)");
    $stmt->bind_param("i", $recent_userID);
    

    if (!$stmt->execute()) {
      
      echo "Error creating seller:".$stmt->error;
      exit;
    }

    echo "Seller created, please login with your credentials.";
  }

  elseif ($accountType === 'buyer'){

    $stmt = $connection->prepare("INSERT INTO buyer (UserID) VALUES (?)");
    $stmt->bind_param("i", $recent_userID);

    if (!$stmt->execute()) {
      
      echo "Error creating Buyer:".$stmt->error;
      exit;
    }

    echo"Buyer created, please login with your credentials.";

  }


  
  //$_SESSION['logged_in'] = true;
	//$_SESSION['username'] = $row['UserID'];
	//$_SESSION['account_type'] = $row['Role'];
  //header('Location: browse.php');
  //echo"$print";
  header("refresh:3;url=index.php");
  exit();
} else {
  echo "Error: " . $stmt->error;
}

?>