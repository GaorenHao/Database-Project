<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My Watchlist Items</h2>

<?php
// Start the session
session_start();

// Check if there are any session variables set
if (!empty($_SESSION)) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
} else {
    echo "No session variables are set.";
}

include 'db_connect.php';
?>


<?php
// Include file1.php
// include 'watchlist_funcs.php';

// $msg = new_bid_watchlist_funcs($item_id, $buyerid, $current_price, $bid);

// Now echo the message
// echo "Message from watchlist_funcs is: " . $msg;




$buyer = $_SESSION['buyerid']; // Specify the buyer

// First Query for the max bid across all users for a given item id
$sql1 = "SELECT WatchListItems.ItemAuctionID, MAX(Bid.BidAmount) AS max_bid_amount
         FROM WatchListItems
         LEFT JOIN Bid ON WatchListItems.ItemAuctionID = Bid.ItemAuctionID
         GROUP BY WatchListItems.ItemAuctionID";
$result1 = $connection->query($sql1);

// Second Query for the max bid a certain user has plaed for a given itemid
$sql2 = "SELECT Bid.ItemAuctionID, MAX(Bid.BidAmount) AS max_bid_per_buyer
         FROM WatchListItems
         LEFT JOIN Bid ON WatchListItems.ItemAuctionID = Bid.ItemAuctionID AND Bid.BuyerID = $buyer
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

// Start of the HTML table
echo "<table border='1'>"; // Adding border for visibility, you can style it as needed
echo "<tr><th>Item ID</th><th>My Highest Bid</th><th>Current Highest Bid</th><th>Status</th></tr>"; // Table headers

// Display the merged results in table rows
foreach ($mergedResults as $row) {
    echo "<tr>";
    echo "<td>" . $row['ItemAuctionID'] . "</td>";
    echo "<td>" . $row['max_bid_per_buyer'] . "</td>";
    echo "<td>" . $row['max_bid_amount'] . "</td>";
    echo "<td>" . $row['status']  . "</td>";
    echo "</tr>";
}

// End of the table
echo "</table>";

?>