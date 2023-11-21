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
include_once('watchlist_funcs.php');

// $msg = new_bid_watchlist_funcs($item_id, $buyerid, $current_price, $bid);

// Now echo the message
// echo "Message from watchlist_funcs is: " . $msg;

$buyer = $_SESSION['buyerid']; // Specify the buyer

$mergedResults = new_bid_watchlist_funcs($connection, $_SESSION['buyerid']);

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