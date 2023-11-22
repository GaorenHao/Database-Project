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
        $stmt->bind_param("i", $UserID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $BuyerID = $row['BuyerID'];

            $stmt = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ?");
            $stmt->bind_param("i", $BuyerID);
            $stmt->execute();
            $result = $stmt->get_result();

            $recommendItemAuctionID = [];

            while ($row = $result->fetch_assoc()) {
                $ItemAuctionID = $row['ItemAuctionID'];

                $stmt = $connection->prepare("SELECT DISTINCT BuyerID FROM Bid WHERE ItemAuctionID = ? AND BuyerID != ?");
                $stmt->bind_param("ii", $ItemAuctionID, $BuyerID);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $otherBuyerID = $row['BuyerID'];

                    $stmt = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ? AND ItemAuctionID != ?");
                    $stmt->bind_param("ii", $otherBuyerID, $ItemAuctionID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $stmt = $connection->prepare("SELECT Title, Description, StartingPrice, EndDate From AuctionItem WHERE ItemAuctionID = ?");
                    $stmt->bind_param("i", $itemAuctionID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $recommendedItemAuctionIDs[] = $row['ItemAuctionID'];
                        echo "<li>";
                        echo "<h3>" . htmlspecialchars($row['Title']) . "</h3>";
                        echo "<p>Description: " . htmlspecialchars($row['Description']) . "</p>";
                        echo "<p>Starting Price: " . htmlspecialchars($row['StartingPrice']) . "</p>";
                        echo "<p>End Date: " . htmlspecialchars($row['EndDate']) . "</p>";
                        echo "</li>";
                    }
                }
            }


        } else {
            echo "You are not a buyer.";
        }
    } else {
        echo "You do not have access.";
    }
    ?>
</div>
