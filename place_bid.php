<?php include_once("header.php")?>
<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';


/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */

var_dump($_POST);
session_start();
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

if (isset($_SESSION['username'])) {
    echo $_SESSION['username'];
} else {
    echo 'Username is not set in the session.';
}

// Create the DateTime object
$now = new DateTime();
// Format the DateTime object to a string
$formattedNow = $now->format('Y-m-d H:i:s');
$buyerid = $_SESSION['buyerid']; 



//// BID LOGIC >>>> NEW BIDS MUST BE HIGHER THAN THE CURRENT HIGHEST BID

$bid = $_POST['bid']; 
$item_id = $_POST['item_id'];
$current_price = $_POST['current_price'];

if ($bid < $current_price) {
  // Handle the error
  // Redirect back to the form with an error message or display the message directly
  echo('Your bid is too low. Please submit a bid higher than the current bid. Redirecting you back to the listing ... ');
} else {
    // insert data into the table 
  $stmt = $connection->prepare("INSERT INTO Bid (BuyerID, ItemAuctionID, BidTime, BidAmount) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iisd", $buyerid, $item_id, $formattedNow, $bid);

  // Execute the prepared statement
  if ($stmt->execute()) {
    echo "New bid added successfully.";
  } else {
    echo "Error: " . $stmt->error;
  }


  // If all is successful, let user know.
  echo('<div class="text-center"><a href="FIXME">View your new listing.</a></div>');
}






?>

?>