<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php

include 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
   // TODO: Check user's credentials (cookie/session).

if (isset ($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'seller'){

  $UserID = $_SESSION['username'];

  $stmt = $connection -> prepare("SELECT SellerID FROM Sellers WHERE UserID = ?");
  if($stmt){

    $stmt-> bind_param("i", $UserID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows>0){
      $row = $result->fetch_assoc();
      $SellerID = $row['SellerID'];
  
  // TODO: Perform a query to pull up their auctions.

      $stmt = $connection->prepare("SELECT ItemAuctionID, Title, Description, StartingPrice, ReservePrice, EndDate  FROM AuctionItem WHERE SellerID = ?");
      if($stmt){
        $stmt->bind_param("i", $SellerID);
        $stmt->execute();
        $result = $stmt->get_result();

         // TODO: Loop through results and print them out as list items.

        if ($result->num_rows>0){
          while ($row = $result->fetch_assoc()){
            $title = $row['Title'];
            $description = $row['Description'];
            $startingprice = $row['StartingPrice'];
            $reserveprice = $row['ReservePrice'];
            $enddate = $row['EndDate'];
            $itemAuctionID =$row['ItemAuctionID'];
            
            echo "<a href='edit_auction.php?item_id=" . $itemAuctionID . "' class='btn btn-secondary'>Edit</a>";
            echo "<li>";
            echo "<h3>" . htmlspecialchars($title) . "</h3>";
            echo "<p>Description: " . htmlspecialchars($description) . "</p>";
            echo "<p>Starting Price: " . htmlspecialchars($startingprice) . "</p>";
            echo "<p>Reserve Price: " . htmlspecialchars($reserveprice) . "</p>";
            echo "<p>End Date: " . htmlspecialchars($enddate) . "</p>";
            echo "</li>";
          }
        }else{
          echo "<p>You have no listed items.</p>";
        }
    
      }
      
    }
  }
}
  
?>

<?php include_once("footer.php")?>