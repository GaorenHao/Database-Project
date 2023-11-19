<?php include_once("header.php")?>
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Received data: " . print_r($_POST, true));

include 'db_connect.php';

if (!isset($_POST['functionname']) || !isset($_POST['item_id'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = $_POST['item_id'];
print_r($item_id);

if (isset($_SESSION['buyerid'])) {
  $buyerId = $_SESSION['buyerid'];
  echo "Buyer ID: " . $buyerId;
  // You can now use $buyerId in your PHP script as needed
} else {
  echo "Buyer ID is not set in the session.";
}

//$watchListId = 1;

if ($_POST['functionname'] == "add_to_watchlist") {
  $WatchlistItems_insert_query = $connection -> prepare("INSERT INTO WatchlistItems (BuyerID, ItemAuctionID) VALUES (?, ?)");
  //$stmt = $connection->prepare("INSERT INTO Watchlist (avaisconfused) VALUES (?, ?, ?, ?, ?)");
  $WatchlistItems_insert_query->bind_param("ii", $buyerId, $item_id);
  // TODO: Update database and return success/failure.

  if ($WatchlistItems_insert_query->execute()) {
    echo "Watchlist added successfully.";
  } else {
    echo "Error: " . $WatchlistItems_insert_query->error;
  }

  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.


  $res = "success";
}


// identifying highest bid & aim out put a message that says its done, to be printed to the watchlist page 


// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

// Define a function
/* function highest_bid_watchlist() {
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
    echo "Greetings from file1!";
}
?> */

function new_bid_watchlist_funcs($item_id, $buyerid, $current_price, $bid) {
  $msg = "The info of the item that just had a new bid are: " . $item_id . ", " . $buyerid . ", " . $current_price . ", " . $bid;
  return $msg;
}
?>