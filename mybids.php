<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">
<?php $buyerid = $_SESSION['buyerid']; ?>
<h2 class="my-3">My Bids</h2>
  
<p>All historical bids placed by Buyer ID <?php echo $buyerid; ?></p2>

<?php
  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  include 'db_connect.php';
  
  // TODO: Check user's credentials (cookie/session).
  if (session_status() == PHP_SESSION_ACTIVE) {
    echo "<pre>";
    //print_r($_SESSION);
    echo "</pre>";
  } else {
      echo "Session is not active, cannot print session variables";
  }
  //echo $buyerid = $_SESSION['buyerid'];
  
  // TODO: Perform a query to pull up the auctions they've bidded on.

  
  // TODO: Loop through results and print them out as list items.

?>


  <!-- TABLE OF ALL BIDS -->
  <table>
          <tbody>
          <?php
                  //$mybids_query = "SELECT * FROM Bid WHERE BuyerID = $buyerid";
                  // ordered by time within id
                   $mybids_query = "SELECT Bid.BidAmount, Bid.ItemAuctionID, Bid.BidTime, AuctionItem.Title, AuctionItem.EndDate, AuctionItem.ReservePrice, Bid.BuyerID 
                   FROM Bid JOIN AuctionItem ON Bid.ItemAuctionID = AuctionItem.ItemAuctionID
                   ORDER BY Bid.ItemAuctionID, Bid.BidTime DESC;";

                   // ordering where the most recent transaction appears first and then any previous transactions of that item id are grouped together,
                   $mybids_query = "SELECT 
                   Bid.BidAmount, 
                   Bid.ItemAuctionID, 
                   Bid.BidTime, 
                   AuctionItem.Title, 
                   AuctionItem.EndDate, 
                   AuctionItem.ReservePrice, 
                   Bid.BuyerID,
                   most_recent.MaxBidTime
                 FROM 
                   Bid 
                 JOIN AuctionItem ON Bid.ItemAuctionID = AuctionItem.ItemAuctionID
                 JOIN (
                     SELECT 
                       ItemAuctionID, 
                       MAX(BidTime) as MaxBidTime
                     FROM 
                       Bid 
                     GROUP BY 
                       ItemAuctionID
                 ) most_recent ON Bid.ItemAuctionID = most_recent.ItemAuctionID
                 ORDER BY 
                   most_recent.MaxBidTime DESC, 
                   Bid.ItemAuctionID, 
                   Bid.BidTime DESC;
                 
            ";
                   $result = $connection->query($mybids_query);

                   if ($result !== false && $result->num_rows > 0) {
                    // Start the table
                    echo "<table border='1'>";
                    echo "<tr><th>My Bid</th><th>Item ID</th><th>Bid Time</th><th>Item Name</th><th>Listing Status</th><th>My Bid Status</th></tr>";
                
                    // Fetch and display each row
                    while ($row = $result->fetch_assoc()) {

                      if ($buyerid == $row['BuyerID']) {

                          $now = new DateTime();
                          $endDate = new DateTime($row["EndDate"]); // Assuming $row["EndDate"] is a valid date string
                          
                          $listingStatus = ($now < $endDate) ? "Ongoing" : "Ended";
                        
                          // Listing Status logic - compare bid time vs. end time
                          //$listingStatus = ($row["BidTime"] < $row["EndDate"]) ? "Ongoing" : "Ended";

                          // identifying the highest bid for that item_id
                          $item_id = $row["ItemAuctionID"];
                          // does not differentiate between the highest bid and other bids by the same buyer... basically gets all of them ... 

                          //// prob need to comment this out! 
                          $highestbid_query = "SELECT MAX(BidAmount) as maxbid FROM Bid WHERE ItemAuctionID = $item_id;";
                          $result2 = $connection->query($highestbid_query);
                          $highestBidRow = $result2->fetch_assoc();
                          $highestBid = $highestBidRow["maxbid"];

                          // This is the highest bid made by the buyer
                          $buyerHighestBid_query = "SELECT MAX(BidAmount) as buyerMaxBid FROM Bid WHERE ItemAuctionID = $item_id AND BuyerID = $buyerid;";
                          $buyerResult = $connection->query($buyerHighestBid_query);
                          $buyerHighestBidRow = $buyerResult->fetch_assoc();
                          $buyerHighestBid = $buyerHighestBidRow["buyerMaxBid"];

                          //$bidStatus = ($row["BidAmount"] >= $highestBid) ? "Highest" : "NA";

                          if ($listingStatus == "Ended" && $highestBid < $row["ReservePrice"]) {
                            $bidStatus = "Reserve not met, item not sold :(";
                          } elseif ($listingStatus == "Ended" && $highestBid >= $row["ReservePrice"] && $row["BidAmount"] == $buyerHighestBid) {
                            $bidStatus = "YOU ARE THE WINNER OF THIS ITEM. This is your winning bid.";
                          } elseif ($listingStatus == "Ongoing" && $row["BidAmount"] >= $highestBid) {
                            $bidStatus = "Current Highest";
                          } else {$bidStatus = " ";}


                          // Bid Status - comparis
                            echo "<tr>";
                            echo "<td>" . $row["BidAmount"] . "</td>";
                            echo "<td>" . $row["ItemAuctionID"] . "</td>";
                            echo "<td>" . $row["BidTime"] . "</td>";
                            echo "<td>" . $row["Title"] . "</td>";
                            echo "<td>" . $listingStatus . "</td>";
                            echo "<td>" . $bidStatus . "</td>";
                            echo "</tr>";

                      }

                      
                    }
                
                        // End the table
                        echo "</table>";
                    } else {
                        echo "0 results";
                    }
                  
                   


            ?>
          </tbody>
          
      </table>

<?php include_once("footer.php")?>