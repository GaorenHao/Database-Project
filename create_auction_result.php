<?php include_once("header.php")?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */
            
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
$auctionTitle = $_POST['auctionTitle']; // no title yet in our database... insert this into Description in our database
$auctionDetails = $_POST['auctionDetails']; // and leave this one out for now :) 
$auctionCategory = $_POST['auctionCategory']; // hmm... do we need to connect the database options to here? maybe leave it out for now ... 
$auctionStartPrice = $_POST['auctionStartPrice'];
$auctionReservePrice = $_POST['auctionReservePrice'];
$auctionEndDate = $_POST['auctionEndDate'];


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */

$stmt = $connection->prepare("INSERT INTO AuctionItem ( SellerID, CategoryID, Description, StartingPrice, ReservePrice, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss",  $_SESSION['username'], $auctionCategory, $auctionTitle, $auctionStartPrice, $auctionReservePrice, $auctionEndDate);

// Execute the prepared statement
if ($stmt->execute()) {
  echo "New item added successfully.";
} else {
  echo "Error: " . $stmt->error;
}
            

// If all is successful, let user know.
echo('<div class="text-center"><a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>