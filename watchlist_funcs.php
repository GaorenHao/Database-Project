<?php ob_start();
include_once("header.php");

?>
<?php
// Start output buffering


/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Received data: " . print_r($_POST, true)); */

include 'db_connect.php';

if (!isset($_POST['functionname']) || !isset($_POST['item_id'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = $_POST['item_id'];
//print_r($item_id);

if (isset($_SESSION['buyerid'])) {
  $buyerId = $_SESSION['buyerid'];
  //echo "Buyer ID: " . $buyerId;
  // You can now use $buyerId in your PHP script as needed
} else {
  //echo "Buyer ID is not set in the session.";
}

/////////////////////// ADDING & REMOVING FROM WATCHLIST 

if ($_POST['functionname'] == "add_to_watchlist") {
  $WatchlistItems_insert_query = $connection -> prepare("INSERT INTO WatchlistItems (BuyerID, ItemAuctionID) VALUES (?, ?)");
  $WatchlistItems_insert_query->bind_param("ii", $buyerId, $item_id);
  // TODO: Update database and return success/failure.

  if ($WatchlistItems_insert_query->execute()) {
    ob_end_clean();
    echo "success";
    exit();
  } else {
    ob_end_clean();
    echo "error";
    exit(); 
  }

}
else if ($_POST['functionname'] == "remove_from_watchlist") {

  $WatchlistItems_delete_query = $connection->prepare("DELETE FROM WatchlistItems WHERE BuyerID = ? AND ItemAuctionID = ?");
  $WatchlistItems_delete_query->bind_param("ii", $buyerId, $item_id);
  $WatchlistItems_delete_query->execute();
  
  // Check if the delete operation was successful
  if ($WatchlistItems_delete_query->affected_rows > 0) {
    ob_end_clean();
    echo "success";
    exit();
  } else {
    ob_end_clean();
    echo "error";
    exit(); 
  }

}


// identifying highest bid & aim out put a message that says its done, to be printed to the watchlist page 


// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
//echo $res;

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

function new_bid_watchlist_funcs($connection, $buyer) {

  // First Query for the max bid across all users for a given item id
  $sql1 = "SELECT WatchListItems.ItemAuctionID, MAX(Bid.BidAmount) AS max_bid_amount
  FROM WatchListItems
  LEFT JOIN Bid ON WatchListItems.ItemAuctionID = Bid.ItemAuctionID
  WHERE WatchListItems.BuyerID = $buyer
  GROUP BY WatchListItems.ItemAuctionID";
  $result1 = $connection->query($sql1);

  // Second Query for the max bid a certain user has plaed for a given itemid
  $sql2 = "SELECT Bid.ItemAuctionID, MAX(Bid.BidAmount) AS max_bid_per_buyer
  FROM WatchListItems
  LEFT JOIN Bid ON WatchListItems.ItemAuctionID = Bid.ItemAuctionID AND Bid.BuyerID = $buyer
  WHERE WatchListItems.BuyerID = $buyer AND Bid.BuyerID = $buyer
  GROUP BY WatchListItems.ItemAuctionID";
  $result2 = $connection->query($sql2);

  // Fetch results
  $results1 = $result1->fetch_all(MYSQLI_ASSOC);
  $results2 = $result2->fetch_all(MYSQLI_ASSOC);



  // Merge Results --- probably item5 is not in here ! 
  $mergedResults = [];

  // First, merge the results based on matching ItemAuctionID - where both tables have something 
  foreach ($results1 as $row1) {
  foreach ($results2 as $row2) {
  if ($row1['ItemAuctionID'] == $row2['ItemAuctionID']) {
  $status = ($row1['max_bid_amount'] > $row2['max_bid_per_buyer']) ? "You have been outbid :(" : "You are the highest bidder!";  
  $mergedResults[$row1['ItemAuctionID']] = [
        'ItemAuctionID' => $row1['ItemAuctionID'],
        'max_bid_amount' => $row1['max_bid_amount'],
        'max_bid_per_buyer' => $row2['max_bid_per_buyer'],
        'status' => $status
        
    ];
  }
  }
  }

  // Add unique entries from results1
  foreach ($results1 as $row1) {
  if (!array_key_exists($row1['ItemAuctionID'], $mergedResults)) {
  $mergedResults[$row1['ItemAuctionID']] = [
    'ItemAuctionID' => $row1['ItemAuctionID'],
    'max_bid_amount' => $row1['max_bid_amount'],
    'max_bid_per_buyer' => "None yet", // Assuming null if not present in $results2 - aka, where buyer has not bidded anything, but is on the watch list 
    'status' => "You have not yet made a bid."
  ];
  }
  }

  // If you need the results to be re-indexed numerically
  $mergedResults = array_values($mergedResults);

  // Return the merged results
  return $mergedResults;

}
  

?>