<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php
include 'db_connect.php';
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  //Ava: we want it to select the items from the auction table that correspond to the Seller ID that is currently logged in.
  



  
  
  // TODO: Check user's credentials (cookie/session).
  
  session_start();
  if ($_SESSION['logged_in'] == true){
    $SellerID = $SESSION['username']
  }
  $_SESSION['username'] = $row['SellerID'];
  $_SESSION['account_type'] = $row['Role'];

  // TODO: Perform a query to pull up their auctions.

  $stmt = $connection->prepare("SELECT `SellerID` FROM `AuctionItem` WHERE `SellerID` = ?");
  $stmt->bind_param("i", $SESSION['username']);
  $stmt->execute();
  $result = $stmt->get_result();
  


  // TODO: Loop through results and print them out as list items.
  
  if ($result->num_rows>0){
    while ($row = $result->fetch_assoc());
    echo "Item:". $row['Title']. "<br>";
  }
  else{
    echo "You have no listed items"

  }
?>

<?php include_once("footer.php")?>