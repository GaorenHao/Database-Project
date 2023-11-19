<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  include 'db_connect.php';

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  // TODO: Check user's credentials (cookie/session).


if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'buyer') {

    $UserID = $_SESSION['username'];

    $stmt->bind_param("i", $UserID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ava: now find the buyerID from the users table
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $BuyerID = $row['BuyerID'];

        // Ava: now that we have the BuyerID we want to check which ItemAuctionID they have bid on 
        $stmt = $connection->prepare("SELECT ItemAuctionID FROM Bid WHERE BuyerID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $BuyerID);
            $stmt->execute();
            $result = $stmt->get_result();

            // Ava: we now know which ItemAuctionID they have bid on so we need to check which other buyerID bid on the same ItemAuctionID
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $itemAuctionID = $row['ItemAuctionID'];

                $stmt = $connection->prepare('SELECT BuyerID FROM Bid WHERE ItemAuctionID = ?');
                if ($stmt) {
                    $stmt->bind_param('i', $itemAuctionID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Ava: now we need to check what ItemAuctionID those BuyerID bid on
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $BuyerID2 = $row['BuyerID'];

                        $stmt = $connection->prepare('SELECT ItemAuctionID FROM Bids WHERE BuyerID =?');
                        if ($stmt) {
                            $stmt->bind_param('i', $BuyerID2);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $itemAuctionID2 = $row['ItemAuctionID'];

                                // Ava: Now we want to go to the Auction Item Table and extract the product descriptions
                                $stmt = $connection->prepare('SELECT Title, Description, StartingPrice, EndDate FROM AuctionItem WHERE ItemAuctionID = ?');
                                if ($stmt) {
                                    $stmt->bind_param('i', $itemAuctionID2);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    // Ava: print out the results yay
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $title = $row['Title'];
                                            $description = $row['Description'];
                                            $startingprice = $row['StartingPrice'];
                                            $enddate = $row['EndDate'];

                                            echo "<li>";
                                            echo "<h3>" . htmlspecialchars($title) . "</h3>";
                                            echo "<p>Description: " . htmlspecialchars($description) . "</p>";
                                            echo "<p>Starting Price: " . htmlspecialchars($startingprice) . "</p>";
                                            echo "<p>End Date: " . htmlspecialchars($enddate) . "</p>";
                                            echo "</li>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    // TODO: Perform a query to pull up auctions they might be interested in.
    // TODO: Loop through results and print them out as list items.
}





  
    
      
  
  // TODO: Perform a query to pull up auctions they might be interested in.
  
  // TODO: Loop through results and print them out as list items.
  
?>