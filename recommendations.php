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

        // Prepare statement for BuyerID
        $stmt = $connection->prepare('SELECT BuyerID FROM Buyer WHERE UserID = ?');
        if (!$stmt) {
            die("Prepare failed: " . htmlspecialchars($connection->error));
        }

        // Bind and execute
        $stmt->bind_param("i", $UserID); // Ensure the data type is correct
        if (!$stmt->execute()) {
            die("Execute failed: " . htmlspecialchars($stmt->error));
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $BuyerID = $row['BuyerID'];

            // Prepare statement for ItemAuctionIDs
            $stmt = $connection->prepare("SELECT DISTINCT ItemAuctionID FROM Bid WHERE BuyerID = ?");
            if (!$stmt) {
                die("Prepare failed: " . htmlspecialchars($connection->error));
            }
            $stmt->bind_param("i", $BuyerID);
            if (!$stmt->execute()) {
                die("Execute failed: " . htmlspecialchars($stmt->error));
            }
            $result = $stmt->get_result();

            $ItemAuctionIDs = [];
            while ($row = $result->fetch_assoc()) {
                $ItemAuctionIDs[] = $row['ItemAuctionID'];
            }

            foreach ($ItemAuctionIDs as $ItemAuctionID) {
                
                $stmt = $connection->prepare("SELECT DISTINCT BuyerID FROM Bid WHERE ItemAuctionID = ? AND BuyerID != ?");
                if (!$stmt) {
                    die("Prepare failed: " . htmlspecialchars($connection->error));
                }
                $stmt->bind_param("ii", $ItemAuctionID, $BuyerID);
                if (!$stmt->execute()) {
                    die("Execute failed: " . htmlspecialchars($stmt->error));
                }
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $otherBuyerID = $row['BuyerID'];

                    
                    $stmt = $connection->prepare("SELECT Title, Description, StartingPrice, EndDate FROM AuctionItem WHERE ItemAuctionID = ?");
                    if (!$stmt) {
                        die("Prepare failed: " . htmlspecialchars($connection->error));
                    }
                    $stmt->bind_param("i", $otherBuyerID);
                    if (!$stmt->execute()) {
                        die("Execute failed: " . htmlspecialchars($stmt->error));
                    }
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $title = $row['Title'];
                            $description = $row['Description'];
                            $startingprice = $row['StartingPrice'];
                            $enddate = $row['EndDate'];

                            echo "<li>";
                            echo "<h3>" . htmlspecialchars($title) . "</h3>";
                            echo "<p>Description: " . htmlspecialchars($description) . "</p>";
                            echo "<p>Starting Price: " . htmlspecialchars($startingprice) . "</p>";
                            echo "<p>End Date: " . htmlspecialchars($enddate) . "</p>";
                            echo "</li>";
                        }
                    }
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
