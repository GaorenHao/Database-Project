<?php
include_once("header.php");
include 'db_connect.php';

// Check if the item ID is set
if (isset($_GET['item_id'])) {
    $itemAuctionID = intval($_GET['item_id']);

    // Prepare and execute delete query
    $stmt = $connection->prepare("DELETE FROM AuctionItem WHERE ItemAuctionID = ?");
    $stmt->bind_param("i", $itemAuctionID);
    if ($stmt->execute()) {
        echo "Auction item deleted successfully.";
        echo('<div class="text-center"><a href="mylistings.php">View your listing.</a></div>');
    } else {
        echo "Error deleting item: " . $stmt->error;
    }
} else {
    echo "No auction item specified for deletion.";
}
include_once("footer.php");
?>
