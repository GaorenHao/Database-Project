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
  $auctionDetails = $_POST['auctionDetails'];
  $auctionCategory = $_POST['auctionCategory'];  
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
  // Default description if necessay
  if (empty($auctionDetails)) {
    $auctionDetails = "No description is given for this item.";
  }

  // Check if catagory if found in the database
  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $categoryID = $row['CategoryID'];
  } else {
      echo "Category not found";
      exit;
  }

  //// *************************** mapping category name to category id
  $stmt = $connection->prepare("SELECT `CategoryID` FROM `Categories` WHERE `CategoryName` = ?");
  $stmt->bind_param("s", $auctionCategory);
  $stmt->execute();
  $result = $stmt->get_result();

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
  $last_id = $connection->insert_id; // Get the ID of the last inserted item
  $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
  $imageUploadSuccess = true;
  $maxImages = 4;

  if (isset($_FILES['auctionImages'])) {
    $total = count($_FILES['auctionImages']['name']);
    $total = min($total, $maxImages); // Process at most 4 images

    for ($i = 0; $i < $total; $i++) {
        // Check if a file is uploaded
        if (!empty($_FILES['auctionImages']['name'][$i])) {
            $tmpFilePath = $_FILES['auctionImages']['tmp_name'][$i];
            $fileType = mime_content_type($tmpFilePath);

            if (in_array($fileType, $allowedMimeTypes)) {
                $newFilePath = "./uploads/" . basename($_FILES['auctionImages']['name'][$i]);
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $stmt = $connection->prepare("INSERT INTO ItemImages (ItemAuctionID, ImagePath) VALUES (?, ?)");
                    $stmt->bind_param("is", $last_id, $newFilePath);
                    if (!$stmt->execute()) {
                        // Handle the case where the image insert query fails
                        echo "Failed to save image data: " . $stmt->error;
                        $imageUploadSuccess = false;
                    }
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
}

  if ($imageUploadSuccess) {
      echo('<div class="text-center"><a href="mylistings.php">View your listing.</a></div>');
  } else {
      echo "Some images failed to upload.";
  }



?>

</div>


<?php include_once("footer.php")?>