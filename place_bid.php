<?php include_once("header.php")?>
<?php include_once("watchlist_funcs.php")?>
<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.


ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

include 'db_connect.php';


/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
$previous_highest_buyerid = $_POST['previous_highest_buyerid'];

if ($bid <= $current_price) {
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

    // check if this item_id & buyerid pair is in watchlist already. add if not. 
    $checkwatchlist_stmt = $connection->prepare("SELECT * FROM WatchListItems WHERE BuyerID = ? AND ItemAuctionID = ?");
    $checkwatchlist_stmt->bind_param("ii", $buyerid, $item_id); // Assuming both IDs are strings; use "ii" if they are integers
    $checkwatchlist_stmt->execute();
    $result = $checkwatchlist_stmt->get_result();

    // If the pair doesn't exist, insert it
    if ($result->num_rows == 0) {
        $insertwatchlist_query = "INSERT INTO WatchListItems (BuyerID, ItemAuctionID) VALUES (?, ?)";
        $insertwatchlist_stmt = $connection->prepare($insertwatchlist_query);
        $insertwatchlist_stmt->bind_param("ii", $buyerid, $item_id); 
        $insertwatchlist_stmt->execute();

        if ($insertwatchlist_stmt->affected_rows > 0) {
            echo "New watchlisted created successfully";
        } else {
            echo "Error: " . $insertwatchlist_stmt->error;
        }
    } else {
        echo "Already in watchlist :)";
    }


    // identify all other buyers who have watchlisted this item... 
    // $other_buyer_query = "SELECT BuyerID FROM WatchListItems WHERE ItemAuctionID = $item_id AND BuyerID <> $buyerid"; 
    // results a list of all other buyerIDs who have watchlisted this item ^^ 

    // need to send notification to all other watchlist buyerIDs, that there has been a new bid. (except for the previous highest buyer, who will get a different message)
    // need to do mapping between buyerID <-> userID... so this via join
    // this will give us a list of userid
    $other_buyer_query = "SELECT Buyer.UserID 
    FROM WatchListItems JOIN Buyer ON WatchListItems.BuyerID = Buyer.BuyerID 
    WHERE WatchListItems.ItemAuctionID = $item_id AND Buyer.BuyerID <> $buyerid AND Buyer.BuyerID <> $previous_highest_buyerid"; 

    $result = mysqli_query($connection, $other_buyer_query);
    if ($result) {
        /// go through one row at a time
        while ($row = mysqli_fetch_assoc($result)) {
            $userId = $row['UserID'];
            $message = "Your watchlist item $item_id has a new bid at $bid.";
            $type = "Watchlist General Update";

            // Insert into notifications table
            $insert_notif = $connection->prepare("INSERT INTO Notification (UserID, DateTime, Message, Type) VALUES (?, ?, ?, ?)");
            $insert_notif->bind_param("isss", $userId, $formattedNow, $message, $type);

            // Execute the prepared statement
            
        }
    } else {
        echo "Error: " . mysqli_error($connection);
    }

    // if the current buyerid =/= previous buyerid, then we need to create a notification to alert that the buyer has been outbid ... 

    // map the previous buyer id to user id
    if ($buyerid != $previous_highest_buyerid) {
        $userid_map_query = "SELECT UserID FROM Buyer WHERE BuyerID = $previous_highest_buyerid";
        $result = mysqli_query($connection, $userid_map_query);
    
        if ($result) {
            $row = mysqli_fetch_assoc($result);

            $userId = $row['UserID'];
            $message = "Your watchlist item $item_id has been outbid. You were previously the highest bidder, but the highest bid is now $bid.";
            $type = "Watchlist Winner Outbid";
    
            // Insert into notifications table
            $insert_notif = $connection->prepare("INSERT INTO Notification (UserID, DateTime, Message, Type) VALUES (?, ?, ?, ?)");
            $insert_notif->bind_param("isss", $userId, $formattedNow, $message, $type);

            // Execute the prepared statement
            if ($insert_notif->execute()) {
                echo "Notification type '$type' inserted successfully";
            } else {
                echo "Error: " . $insert_notif->error;
            }
        } else {
            echo "Error: " . mysqli_error($connection);
        }
    }
    include('send_notification.php');
    // Assign values to session variables
    $_SESSION['most_recent_item_id'] = $item_id;
    $_SESSION['current_price'] = $current_price;
    $_SESSION['most_recent_bid'] = $bid;
  } else {
    echo "Error: " . $stmt->error;
  }




  // If all is successful, let user know.
  echo('<div class="text-center"><a href="mybids.php">View your new listing.</a></div>');
}






?>
