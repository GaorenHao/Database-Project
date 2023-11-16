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
if (isset ($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'seller'){

  $UserID = $_SESSION['username'];



  $stmt = $connection -> prepare("SELECT 'SellerID' FROM 'Sellers' WHERE 'UserID' = ?");
  $stmt-> bind_param("i", $UserID);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows>0){
    $row = $result->fetch_assoc();
    $SellerID = $row['SellerID']
  

  
  // TODO: Perform a query to pull up their auctions.

  $stmt = $connection->prepare("SELECT `Title` FROM `AuctionItem` WHERE `SellerID` = ?");
  $stmt->bind_param("i", $SellerID);
  $stmt->execute();
  $result = $stmt->get_result();

    if ($result->num_rows>0){
      while ($row = $result->fetch_assoc()){
      echo "Item:". $row['Title']. "<br>";
    }
    }else{
      echo "You have no listed items"
  
    }
  }
}



  

  


  // TODO: Loop through results and print them out as list items.
  
  
?>

<?php include_once("footer.php")?>