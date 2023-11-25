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
  $auctionStartPrice = floatval($_POST['auctionStartPrice']); 

  // Check if reserve price is provided and ensure it's a number
  if (empty($_POST['auctionReservePrice'])) {
    $auctionReservePrice = $auctionStartPrice;
  } else {
    $auctionReservePrice = floatval($_POST['auctionReservePrice']);
    if ($auctionReservePrice < $auctionStartPrice) {
      echo "Error: Reserve price must be higher than the starting price.";
      exit;
    }
  }
  if (empty($auctionDetails)) {
    $auctionDetails = "No description is given for this item.";
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
  
  // After inserting the auction item
  //$last_id = $connection->insert_id; // Get the ID of the last inserted item
  //$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
  //$imageUploadSuccess = true;

  /* if (isset($_FILES['auctionImages'])) {
      $total = count($_FILES['auctionImages']['name']);

      for ($i = 0; $i < $total; $i++) {
          $tmpFilePath = $_FILES['auctionImages']['tmp_name'][$i];
          $fileType = mime_content_type($tmpFilePath);

          if (in_array($fileType, $allowedMimeTypes)) {
              $newFilePath = "./uploads/" . basename($_FILES['auctionImages']['name'][$i]);
              if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                  $stmt = $connection->prepare("INSERT INTO ItemImages (ItemAuctionID, ImagePath) VALUES (?, ?)");
                  $stmt->bind_param("is", $last_id, $newFilePath);
                  $stmt->execute();
              } else {
                  echo "Failed to upload image: " . htmlspecialchars($_FILES['auctionImages']['name'][$i]);
                  $imageUploadSuccess = false;
              }
          } else {
              echo "Invalid file type: " . htmlspecialchars($_FILES['auctionImages']['name'][$i]);
              $imageUploadSuccess = false;
          }
      }
  }

  if ($imageUploadSuccess) {
      echo('<div class="text-center"><a href="mylistings.php">View your listing.</a></div>');
  } else {
      echo "Some images failed to upload."; */
      // Optional: Consider rolling back the auction item insertion if image upload is critical
  //}
?>

</div>


<?php include_once("footer.php")?>