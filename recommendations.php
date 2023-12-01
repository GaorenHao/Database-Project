<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">
    <h2 class="my-3">Recommendations</h2>
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
        $result1 = $stmt->get_result();

        if ($result1->num_rows == 0) {
            echo "You are not a buyer.";
        } else {
            $row1 = $result1->fetch_assoc();
            $BuyerID = $row1['BuyerID'];

            $stmt = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ?");
            $stmt->bind_param("i", $BuyerID);
            $stmt->execute();
            $result2 = $stmt->get_result();

            $recommendations = false; 

            while ($row2 = $result2->fetch_assoc()) {
                $ItemAuctionID = $row2['ItemAuctionID'];

                $stmt = $connection->prepare("SELECT DISTINCT BuyerID FROM Bid WHERE ItemAuctionID = ? AND BuyerID != ?");
                $stmt->bind_param("ii", $ItemAuctionID, $BuyerID);
                $stmt->execute();
                $result3 = $stmt->get_result();

                while ($row3 = $result3->fetch_assoc()) {
                    $buyerID = $row3['BuyerID'];

                    $stmt = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ? AND ItemAuctionID != ?");
                    $stmt->bind_param("ii", $buyerID, $ItemAuctionID);
                    $stmt->execute();
                    $result4 = $stmt->get_result();

                    if ($result4->num_rows > 0) {
                        $recommendations = true; 

                        while ($row4 = $result4->fetch_assoc()) {
                            $itemAuctionID = $row4["ItemAuctionID"];

                            $stmt = $connection->prepare("SELECT Title, Description, StartingPrice, EndDate From AuctionItem WHERE ItemAuctionID = ?");
                            $stmt->bind_param("i", $itemAuctionID);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            while ($row = $result->fetch_assoc()) {
                                echo "<li>";
                                echo "<h3>" . htmlspecialchars($row['Title']) . "</h3>";
                                echo "<p>Description: " . htmlspecialchars($row['Description']) . "</p>";
                                echo "<p>Starting Price: " . htmlspecialchars($row['StartingPrice']) . "</p>";
                                echo "<p>End Date: " . htmlspecialchars($row['EndDate']) . "</p>";
                                echo "</li>";
                            }
                        }
                    }
                }
            }

            if (!$recommendations) {
                echo "You have no recommended items yet. Place a bid and come back to this page.";
            }
        }
    } else {
        echo "You do not have access.";
    }
    ?>
</div>
