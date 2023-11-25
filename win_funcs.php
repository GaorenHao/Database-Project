<?php
function check_ending_listings($connection, $userId) {
  // we need the fetch all the listings with end time between these two... 
  // represents listings that have ended since the user last signed in... that we need to create notificiations for
  // $login_time = $_SESSION['login_time']; // when the user logged in

  // ^^ update... we actually need, the timestamp that the user last logged OUT 
  $lastlogout_query = "SELECT LastLogout FROM Users WHERE UserID = $userId";
  $result = mysqli_query($connection, $lastlogout_query);
    
  if ($result) {
    $row = mysqli_fetch_assoc($result);
    $lastlogout = $row['LastLogout']; 
  } else {
    echo "Error in last logout query ";
  }

  //$formatted_past = $login_time->format('Y-m-d H:i:s');
  $now = new DateTime();
  $formattedNow = $now->format('Y-m-d H:i:s');
  
  // get ended listings
  // $finished_listing_query = "SELECT * FROM AuctionItem WHERE AuctionItem.EndDate BETWEEN $formatted_past AND NOW;"
  // get highest / most recent (should be the same according to logic) bids corresponding only to the listings that have ended
  $winning_bids_query = "SELECT BuyerID, Bid.ItemAuctionID, BidAmount FROM Bid
  INNER JOIN (
  SELECT ItemAuctionID, MAX(BidTime) as MaxBidTime
  FROM Bid GROUP BY ItemAuctionID) AS WinningBids 
  ON Bid.ItemAuctionID = WinningBids.ItemAuctionID 
            AND Bid.BidTime = WinningBids.MaxBidTime";

  // join together by item id
  $winner_match_query = "SELECT AuctionItem.ItemAuctionID, Title, SellerID, StartingPrice, ReservePrice, EndDate, BuyerID, BidAmount 
  FROM AuctionItem
    JOIN ($winning_bids_query) AS MaxBid 
    ON AuctionItem.ItemAuctionID = MaxBid.ItemAuctionID
    WHERE AuctionItem.EndDate BETWEEN $lastlogout AND NOW()";
 
  // might need to do userid mapping here... 
  $winner_id_map_query = "SELECT ItemAuctionID, Title, StartingPrice, ReservePrice, EndDate, Sellers.SellerID, Buyer.BuyerID, 
  BidAmount AS WinningBidAmount, Sellers.UserID AS SellerUserID, Buyer.UserID AS BuyerUserID 
  FROM ($winner_match_query) AS WinningTransactions
  LEFT JOIN Sellers ON Sellers.SellerID = WinningTransactions.SellerID
  LEFT JOIN Buyer ON Buyer.BuyerID = WinningTransactions.BuyerID";

  $winner_id_map_query = "SELECT
    ItemAuctionID,
    Title,
    StartingPrice,
    ReservePrice,
    EndDate,
    Sellers.SellerID,
    Buyer.BuyerID,
    BidAmount AS WinningBidAmount,
    Sellers.UserID AS SellerUserID,
    Buyer.UserID AS BuyerUserID
FROM
    (
    SELECT
        AuctionItem.ItemAuctionID,
        Title,
        SellerID,
        StartingPrice,
        ReservePrice,
        EndDate,
        BuyerID,
        BidAmount
    FROM
        AuctionItem
    JOIN(
        SELECT BuyerID,
            Bid.ItemAuctionID,
            BidAmount
        FROM
            Bid
        INNER JOIN(
            SELECT ItemAuctionID,
                MAX(BidTime) AS MaxBidTime
            FROM
                Bid
            GROUP BY
                ItemAuctionID
        ) AS WinningBids
    ON
        Bid.ItemAuctionID = WinningBids.ItemAuctionID AND Bid.BidTime = WinningBids.MaxBidTime
    ) AS MaxBid
ON
    AuctionItem.ItemAuctionID = MaxBid.ItemAuctionID
WHERE
    AuctionItem.EndDate BETWEEN '2023-11-21 23:12:32' AND NOW()) AS WinningTransactions
LEFT JOIN Sellers ON Sellers.SellerID = WinningTransactions.SellerID
LEFT JOIN Buyer ON Buyer.BuyerID = WinningTransactions.BuyerID;
  ";

  // index through the results, and for every row (every winner buyer / seller pair - create a pair of notifications)
  // need to do userid mapping 

  $result = $connection->query($winner_id_map_query);
  if ($result) {
      // Step 4: Loop through the results
      while ($row = $result->fetch_assoc()) {

        $buyerUserId = $row['BuyerUserID'];
        $buyer_id = $row['BuyerID'];
        $sellerUserId = $row['SellerUserID'];
        $seller_id = $row['SellerID'];

        $item_id = $row['ItemAuctionID'];
        $item_name = $row['Title'];
        $win_bid = $row['WinningBidAmount'];
        $reserve_bid = $row['ReservePrice'];

        if ($win_bid >= $reserve_bid) {

          // create notification for winning buyer
          $buyer_msg = "CONGRATZ Buyer $buyer_id, you are the winner of the $item_name (item $item_id) listed by Seller $seller_id, for $win_bid.";
          $type = "Winning Transaction Buyer";
          // Insert into notifications table

          // check first if the notifications already exist, based on everything other than time
            $no_duplicate_notif_query = "INSERT INTO Notification (UserID, DateTime, Message, Type) SELECT ?, ?, ?, ?
            WHERE NOT EXISTS (SELECT 1 FROM Notification WHERE UserID = ? AND Message = ? AND Type = ?)";

            $insert_notif = $connection->prepare($no_duplicate_notif_query);

            // Bind parameters for INSERT and NOT EXISTS check
            $insert_notif->bind_param("isssiss", $buyerUserId, $formattedNow, $buyer_msg, $type, 
                                        $buyerUserId, $buyer_msg, $type);

          // Execute the prepared statement
          if ($insert_notif->execute()) {
                  //echo "Notification type '$type' inserted successfully";
          } else {
                  echo "Error: " . $insert_notif->error;
          }

          // delete item from everyones watchlist after winning notification is sent
          $WatchlistItems_delete_query = $connection->prepare("DELETE FROM WatchlistItems WHERE ItemAuctionID = ?");
          $WatchlistItems_delete_query->bind_param("i", $item_id);
          $WatchlistItems_delete_query->execute();
          
          // Check if the delete operation was successful
          /* if ($WatchlistItems_delete_query->affected_rows > 0) {
            ob_end_clean();
            echo "success";
            exit();
          } else {
            ob_end_clean();
            echo "error";
            exit(); 
          } */
          

          // create notification for seller of winning transaction
          $seller_msg = "CONGRATZ Seller $seller_id, your listing $item_name (item $item_id) has been sold to Buyer $buyer_id for $win_bid.";
          $type = "Winning Transaction Seller";
          // Insert into notifications table
          $no_duplicate_notif_query = "INSERT INTO Notification (UserID, DateTime, Message, Type) SELECT ?, ?, ?, ?
            WHERE NOT EXISTS (SELECT 1 FROM Notification WHERE UserID = ? AND Message = ? AND Type = ?)";

          $insert_notif = $connection->prepare($no_duplicate_notif_query);

          $insert_notif->bind_param("isssiss", $sellerUserId, $formattedNow, $seller_msg, $type, 
                                $sellerUserId, $seller_msg, $type,);

          // Execute the prepared statement
          if ($insert_notif->execute()) {
                  //echo "Notification type '$type' inserted successfully";
          } else {
                  echo "Error: " . $insert_notif->error;
          }

        }

      }

  } else {
      echo "Error: " . $mysqli->error;
  }
}
?>