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
  $auctionTitle = $_POST['auctionTitle']; 
  $auctionDetails = $_POST['auctionDetails']; // and leave this one out for now :) 
  $auctionCategory = $_POST['auctionCategory']; // hmm... do we need to connect the database options to here? maybe leave it out for now ... 
  $auctionStartPrice = $_POST['auctionStartPrice'];
  $auctionReservePrice = $_POST['auctionReservePrice'];
  $auctionEndDate = $_POST['auctionEndDate'];

  // Convert the auction end date to a DateTime object
  $endDate = DateTime::createFromFormat('Y-m-d\TH:i', $auctionEndDate);
  $now = new DateTime();

  // Check if the end date is in the past
  if ($endDate <= $now) {
      echo "Error: Auction end date must be in the future.";
      exit;
  }
  if (empty($auctionReservePrice)) {
    $auctionReservePrice = $auctionStartPrice;
  } else {
    // If reserve price is entered, check if it's greater than the starting price
    if ($auctionReservePrice <= $auctionStartPrice) {
      echo "Error: Reserve price must be higher than the starting price.";
      exit;
    }
  }

  //$nullWatchlist = 0; //////// we could implement default nulls on the SQL side, but for now, let me just do it cosmetically on this side. 
  /// we are just creating a new item to list. so it is typical that it does not immediately have a watchlistID assignment

  //// *************************** mapping category name to category id
  $stmt = $connection->prepare("SELECT `CategoryID` FROM `Categories` WHERE `CategoryName` = ?");
  $stmt->bind_param("s", $auctionCategory);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $categoryID = $row['CategoryID'];
  } else {
      // Handle the case where the category does not exist
      echo "Category not found";
      exit;
  }

  //// *************************** mapping userid to sellerid
  $stmt = $connection->prepare("SELECT `CategoryID` FROM `Categories` WHERE `CategoryName` = ?");
  $stmt->bind_param("s", $auctionCategory);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $categoryID = $row['CategoryID'];
  } else {
      // Handle the case where the category does not exist
      echo "Category not found";
      exit;
  }


  /* TODO #3: If everything looks good, make the appropriate call to insert
              data into the database. */

  $stmt = $connection->prepare("INSERT INTO AuctionItem ( SellerID, CategoryID, Title, Description, StartingPrice, ReservePrice, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssdis", $_SESSION['sellerid'], $categoryID, $auctionTitle, $auctionDetails, $auctionStartPrice, $auctionReservePrice, $auctionEndDate);

  // Execute the prepared statement
  if ($stmt->execute()) {
    echo "New item added successfully.";
  } else {
    echo "Error: " . $stmt->error;
  }
              

  // If all is successful, let user know.
  echo('<div class="text-center"><a href="mylistings.php">View your listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>