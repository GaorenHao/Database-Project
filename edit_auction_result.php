<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in and is a seller.
if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
  echo "You do not have permission to access this page.";
  exit;
}

// Extract form data
$itemAuctionID = $_POST['itemAuctionID'];
$auctionTitle = $_POST['auctionTitle'];
$auctionDetails = $_POST['auctionDetails'];
$auctionStartPrice = $_POST['auctionStartPrice']; 
$auctionReservePrice = $_POST['auctionReservePrice']; 
$auctionEndDate = $_POST['auctionEndDate'];
$auctionCategoryID = $_POST['auctionCategory']; 
$deleteImages = $_POST['deleteImages'] ?? []; // Images marked for deletion

// Convert the auction end date to a DateTime object
$endDate = DateTime::createFromFormat('Y-m-d\TH:i', $auctionEndDate);
$now = new DateTime();

// Check if the end date is in the past
if ($endDate <= $now) {
    echo "Error: Auction end date must be in the future.";
    exit;
}
$auctionStartPrice = floatval($auctionStartPrice); 
$auctionReservePrice = floatval($auctionReservePrice);

// Begin database transaction
$connection->begin_transaction();

try {
  // Update the database with new details
  $stmt = $connection->prepare("UPDATE AuctionItem SET Title = ?, Description = ?, CategoryID = ?, StartingPrice = ?, ReservePrice = ?, EndDate = ? WHERE ItemAuctionID = ?");
  $stmt->bind_param("ssiddsi", $auctionTitle, $auctionDetails, $auctionCategoryID, $auctionStartPrice, $auctionReservePrice, $auctionEndDate, $itemAuctionID);
  if (!$stmt->execute()) {
      throw new Exception($stmt->error);
  }

  // Delete selected images
  foreach ($deleteImages as $imageID) {
      $stmt = $connection->prepare("DELETE FROM ItemImages WHERE ImageID = ?");
      $stmt->bind_param("i", $imageID);
      if (!$stmt->execute()) {
          throw new Exception($stmt->error);
      }
  }

  // Check the number of existing images for this item
  $stmt = $connection->prepare("SELECT COUNT(*) as imageCount FROM ItemImages WHERE ItemAuctionID = ?");
  $stmt->bind_param("i", $itemAuctionID);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $existingImageCount = $row['imageCount'];

  // Calculate remaining image slots
  $maxImages = 4;
  $remainingSlots = $maxImages - $existingImageCount;

  // Check if any new files are uploaded
  if (!empty($_FILES['auctionImages']['name'][0]) && $remainingSlots > 0) {
      $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
      
      // Limit total number of images processed to remaining slots
      $total = min(count($_FILES['auctionImages']['name']), $remainingSlots);

      for ($i = 0; $i < $total; $i++) {
          $tmpFilePath = $_FILES['auctionImages']['tmp_name'][$i];
          $fileType = mime_content_type($tmpFilePath);

          if (in_array($fileType, $allowedMimeTypes)) {
              $newFilePath = "./uploads/" . basename($_FILES['auctionImages']['name'][$i]);
              if (!move_uploaded_file($tmpFilePath, $newFilePath)) {
                  throw new Exception("Failed to upload image: " . htmlspecialchars($_FILES['auctionImages']['name'][$i]));
              }

              $stmt = $connection->prepare("INSERT INTO ItemImages (ItemAuctionID, ImagePath) VALUES (?, ?)");
              $stmt->bind_param("is", $itemAuctionID, $newFilePath);
              if (!$stmt->execute()) {
                  throw new Exception($stmt->error);
              }
          } else {
              throw new Exception("Invalid file type: " . htmlspecialchars($_FILES['auctionImages']['name'][$i]));
          }
      }
  }

  // Commit transaction
  $connection->commit();
  echo "Auction item updated successfully.";
  echo('<div class="text-center"><a href="mylistings.php">View your listing.</a></div>');

} catch (Exception $e) {
  $connection->rollback();
  echo "Error updating item: " . $e->getMessage();
}

include_once("footer.php");
?>
