<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// Now you can use $connection to interact with the database

var_dump($_POST);
$accountType = $_POST['accountType'];
$email = $_POST['email'];
$password = $_POST['password'];
$passwordConfirmation = $_POST['passwordConfirmation'];

// Assign default values if first name and last name are not provided
$firstName = isset($_POST['firstName']) ? $_POST['firstName'] : 'N/A';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : 'N/A';

$stmt = $connection->prepare("INSERT INTO users (role, email, password, FirstName, LastName) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $accountType, $email, $password, $firstName, $lastName);

// Execute the prepared statement
if ($stmt->execute()) {
  echo "New account registered successfully.";

  if ($accountType==='seller') {
    $lastUserID = $connection->insert_id
    $stmt =  $connection->prepare("INSERT INTO sellers (userID) VALUES (?)");
    $stmt->blind_param("i", $lastUserID)

    if ($stmt->execute()){
      echo "New seller registered successfully. ";
    else {
      echo "Error creating new seller:".stmt->error
      </div>
    }
    }

  }
} else {
  echo "Error: " . $stmt->error;
}

?>