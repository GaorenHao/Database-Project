<?php include_once("header.php")?>
<div class="container my-5">

<?php
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

  // Update the database
  $stmt = $connection->prepare("UPDATE AuctionItem SET Title = ?, Description = ?, StartingPrice = ?, ReservePrice = ?, EndDate = ? WHERE ItemAuctionID = ?");
  $stmt->bind_param("ssddsi", $auctionTitle, $auctionDetails, $auctionStartPrice, $auctionReservePrice, $auctionEndDate, $itemAuctionID);

  if ($stmt->execute()) {
    echo "Auction item updated successfully.";
    echo('<div class="text-center"><a href="mylistings.php">View your listing.</a></div>');
  } else {
    echo "Error updating item: " . $stmt->error;
  }

?>

</div>
<?php include_once("footer.php")?>
