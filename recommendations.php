<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">
    <h2 class="my-3">Recommendations for you</h2>
    <?php
    include 'db_connect.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);



    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'buyer') {
        $UserID = $_SESSION['username'];

        $stmt = $connection->prepare('SELECT BuyerID FROM Buyer WHERE UserID = ?');
        if (!$stmt) {
            die("Prepare failed: " . htmlspecialchars($connection->error));
        }

        $stmt->bind_param("i", $UserID);
        if (!$stmt->execute()) {
            die("Execute failed: " . htmlspecialchars($stmt->error));
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $BuyerID = $row['BuyerID'];

            $stmt = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ?");
            $stmt->bind_param("i", $BuyerID);
            $stmt->execute();
            $result = $stmt->get_result();

            $recommendedItemAuctionIDs = [];
            while ($row = $result->fetch_assoc()) {
                $ItemAuctionID = $row['ItemAuctionID'];

          
                $stmt2 = $connection->prepare("SELECT DISTINCT BuyerID FROM Bid WHERE ItemAuctionID = ? AND BuyerID != ?");
                $stmt2->bind_param("ii", $ItemAuctionID, $BuyerID);
                $stmt2->execute();
                $result2 = $stmt2->get_result();

                while ($row2 = $result2->fetch_assoc()) {
                    $otherBuyerID = $row2['BuyerID'];

                    $stmt3 = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ? AND ItemAuctionID != ?");
                    $stmt3->bind_param("ii", $otherBuyerID, $ItemAuctionID);
                    $stmt3->execute();
                    $result3 = $stmt3->get_result();

                    while ($row3 = $result3->fetch_assoc()) {
                        $recommendedItemAuctionIDs[] = $row3['ItemAuctionID'];
                    }
                }
            }

            $recommendedItemAuctionIDs = array_unique($recommendedItemAuctionIDs);

            foreach ($recommendedItemAuctionIDs as $recItemAuctionID) {
                $stmt4 = $connection->prepare("SELECT Title, Description, StartingPrice, EndDate FROM AuctionItem WHERE ItemAuctionID = ?");
                $stmt4->bind_param("i", $recItemAuctionID); 
                $stmt4->execute();
                $recItemsResult = $stmt4->get_result();

                while ($recItemRow = $recItemsResult->fetch_assoc()) {
                    echo "<li>";
                    echo "<h3>" . htmlspecialchars($recItemRow['Title']) . "</h3>";
                    echo "<p>Description: " . htmlspecialchars($recItemRow['Description']) . "</p>";
                    echo "<p>Starting Price: " . htmlspecialchars($recItemRow['StartingPrice']) . "</p>";
                    echo "<p>End Date: " . htmlspecialchars($recItemRow['EndDate']) . "</p>";
                    echo "</li>";
                }
            }
        } else {
            echo "No BuyerID found for this UserID.";
        }
    } else {
        echo "User is not logged in or not a buyer.";
    }
    ?>
</div>
