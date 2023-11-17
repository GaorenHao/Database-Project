<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My bids</h2>

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
    print_r($_SESSION);
    echo "</pre>";
  } else {
      echo "Session is not active, cannot print session variables";
  }
  echo $buyerid = $_SESSION['buyerid'];
  
  // TODO: Perform a query to pull up the auctions they've bidded on.

  
  // TODO: Loop through results and print them out as list items.

?>


  <!-- TABLE OF ALL BIDS -->
  <table>
          <tbody>
          <?php
                  //$mybids_query = "SELECT * FROM Bid WHERE BuyerID = $buyerid";
                   $mybids_query = "SELECT Bid.BidAmount, Bid.ItemAuctionID, Bid.BidTime, AuctionItem.Title, AuctionItem.EndDate, AuctionItem.ReservePrice, Bid.BuyerID FROM Bid JOIN AuctionItem ON Bid.ItemAuctionID = AuctionItem.ItemAuctionID;";
                   $result = $connection->query($mybids_query);

                   if ($result !== false && $result->num_rows > 0) {
                    // Start the table
                    echo "<table border='1'>";
                    echo "<tr><th>My Bid</th><th>Item ID</th><th>Bid Time</th><th>Item Name</th><th>Listing Status</th><th>My Bid Status</th></tr>";
                
                    // Fetch and display each row
                    while ($row = $result->fetch_assoc()) {

                      if ($buyerid == $row['BuyerID']) {

                          // Listing Status logic - compare bid time vs. end time
                          $listingStatus = ($row["BidTime"] < $row["EndDate"]) ? "Ongoing" : "Ended";

                          // identifying the highest bid for that item_id
                          $item_id = $row["ItemAuctionID"];

                          $highestbid_query = "SELECT MAX(BidAmount) FROM Bid WHERE ItemAuctionID = $item_id;";
                          $result2 = $connection->query($highestbid_query);
                          $highestBidRow = $result2->fetch_assoc();
                          $highestBid = $highestBidRow["MAX(BidAmount)"];

                          //$bidStatus = ($row["BidAmount"] >= $highestBid) ? "Highest" : "NA";

                          if ($listingStatus == "Ended" && $highestBid < $row["ReservePrice"]) {
                            $bidStatus = "Reserve not met, item not sold :(";
                          } elseif ($listingStatus == "Ended" && $highestBid >= $row["ReservePrice"]) {
                            $bidStatus = "YOU ARE THE WINNER";
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