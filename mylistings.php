<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>
<p class="text-center">👇 Click to edit your listed auction item</p>

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

      //$stmt = $connection->prepare("SELECT ItemAuctionID, Title, Description, StartingPrice, ReservePrice, EndDate  FROM AuctionItem WHERE SellerID = ?");

      $stmt_query = "SELECT 
      AuctionItem.ItemAuctionID,
      AuctionItem.Title,
      AuctionItem.Description,
      AuctionItem.StartingPrice,
      AuctionItem.ReservePrice,
      AuctionItem.EndDate,
      Bid.BuyerID, -- BuyerID of the highest bid
      MaxBid.MaxBidAmount -- Highest bid amount
  FROM 
      AuctionItem
  LEFT JOIN 
      (
          SELECT 
              ItemAuctionID, 
              MAX(BidAmount) as MaxBidAmount
          FROM 
              Bid
          GROUP BY 
              ItemAuctionID
      ) MaxBid ON AuctionItem.ItemAuctionID = MaxBid.ItemAuctionID
  LEFT JOIN 
      Bid ON AuctionItem.ItemAuctionID = Bid.ItemAuctionID AND MaxBid.MaxBidAmount = Bid.BidAmount
  WHERE 
      AuctionItem.SellerID = ?
  GROUP BY 
      AuctionItem.ItemAuctionID, Bid.BuyerID;
  
  
      ";

      $stmt = $connection->prepare($stmt_query);


      if($stmt){
        $stmt->bind_param("i", $SellerID);
        $stmt->execute();
        $result = $stmt->get_result();

         // TODO: Loop through results and print them out as list items.

         if ($result->num_rows > 0) {
          echo '<div class="row">'; // Start of row
          while ($row = $result->fetch_assoc()) {
              $now = new DateTime();
              $endDateFormat = new DateTime($row["EndDate"]); // Assuming $row["EndDate"] is a valid date string
              $listingStatus = ($now < $endDateFormat) ? "Ongoing" : "Ended";
              $highestPriceLabel = "Current Price";
              $highestBidderLabel = "Highest Bidder";

              if ($listingStatus === "Ended") {
                if ($row['MaxBidAmount'] >= $row['ReservePrice']) {
                    // If the max bid is equal to or greater than the reserve price
                    $listingStatus = "Ended - SOLD!";
                    $highestPriceLabel = "SOLD FOR";
                    $highestBidderLabel = "SOLD TO BUYER ID";
                } elseif ($row['MaxBidAmount'] < $row['ReservePrice']) {
                    // If the max bid is less than the reserve price
                    $listingStatus = "Ended - Reserve not met";
                }
             }

              $itemLink = "edit_auction.php?item_id=" . $row['ItemAuctionID'];
      
              echo '<div class="col-md-3">';
              echo '<a href="' . $itemLink . '" class="item-link">';
              echo '<div class="item-box">';
      
              // Fetch the first image for the item or use the default image
              $imageSql = "SELECT ImagePath FROM ItemImages WHERE ItemAuctionID = " . $row['ItemAuctionID'] . " LIMIT 1";
              $imageResult = $connection->query($imageSql);
      
              echo '<div class="image-wrapper">'; // Start of image wrapper
              if ($imageResult && $imageResult->num_rows > 0) {
                  $imageRow = $imageResult->fetch_assoc();
                  echo '<img src="' . htmlspecialchars($imageRow['ImagePath']) . '" alt="Item Image" class="item-image">';
              } else {
                  echo '<img src="default.jpg" alt="Default Image" class="item-image">';
              }
              echo '</div>'; // End of image wrapper

              echo '<div style="font-size: 0.75em;">';
              echo "<h6>" . htmlspecialchars($row['Title']) . "</h6>";
              echo "<p class='description'>" . htmlspecialchars($row['Description']) . "</p>";
      
              echo '<div class="item-info">';
              echo "<p>End Date: " . htmlspecialchars($row['EndDate']) . "</p>";
              echo "<p>Starting Price: £" . htmlspecialchars($row['StartingPrice']) . "</p>";
              echo "<p>Reserve Price: £" . htmlspecialchars($row['ReservePrice']) . "</p>";
              echo "<p>" . $highestPriceLabel . ": " . $row['MaxBidAmount'] . "</p>";
              echo "<p>Status: " . htmlspecialchars($listingStatus) . "</p>";
              echo "<p>" . $highestBidderLabel . ": " . $row['BuyerID'] . "</p>";
              echo '</div>'; // End of item-info
              echo '</div>'; // End of item-box
              echo '</a>';
              echo '</div>'; // End of col-md-3
              echo '</div>'; // End of col-md-3
          }
          echo '</div>'; // End of row
      } else {
          echo "<p>You have no listed items.</p>";
      }
      
      }
      
    }
  }
}
  
?>

<?php include_once("footer.php")?>