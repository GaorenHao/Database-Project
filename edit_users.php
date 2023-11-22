<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php

if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'admin') {
    header('Location: browse.php');
  }

?>
<div class="container">
<h2 class="my-3">Manage Users</h2>
<?php include_once("header.php")?>


<!-- Create auction form -->
<form method="POST" action="edit_users.php">
<div class="form-group row">
    <label for="accountType" class="col-sm-2 col-form-label text-right">Deleting a:</label>
	<div class="col-sm-10">
	  <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="accountType" id="accountBuyer" value="buyer" checked>
        <label class="form-check-label" for="accountBuyer">Buyer</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="accountType" id="accountSeller" value="seller">
        <label class="form-check-label" for="accountSeller">Seller</label>
      </div>
      <small id="accountTypeHelp" class="form-text-inline text-muted"><span class="text-danger">* Required.</span></small>
	</div>
  <div class="form-group row">
    <label for="userID" class="col-sm-2 col-form-label text-right">User ID</label>
    <div class="col-sm-10">
      <input type="userID" class="form-control" id="userID" name="userID" placeholder="Enter UserID">
      <small id="userIDHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <label for="userIDConfirmation" class="col-sm-2 col-form-label text-right">Confirm User ID</label>
    <div class="col-sm-10">
      <input type="userID" class="form-control" id="userIDConfirmation" name="userIDConfirmation" placeholder="Enter UserID again">
      <small id="userIDConfirmationHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
    </div>
  </div>
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control">Delete User</button>
  </div>
</form>


</div>





<?php
$accountType = $_POST['accountType'];
$userID = $_POST['userID']; 
$userIDConfirmation= $_POST['userIDConfirmation']; 







if ($userID == $userIDConfirmation) {
    
    if ($accountType == 'buyer') {
        $stmt = $connection->prepare("SELECT BuyerID FROM Buyer WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $BuyerID = $row['BuyerID'];

            $stmt = $connection->prepare("DELETE FROM Notification WHERE UserID = ?");
            $stmt->bind_param("i", $userID);
            if ($stmt->execute()) {
                $stmt = $connection->prepare("DELETE FROM Buyer WHERE UserID = ?");
                $stmt->bind_param("i", $userID);
                if ($stmt->execute()) {
                    $stmt = $connection->prepare("DELETE FROM Users WHERE UserID = ?");
                    $stmt->bind_param("i", $userID);
                    if ($stmt->execute()) {
                        $stmt = $connection->prepare("DELETE FROM Bid WHERE BuyerID = ?");
                        $stmt->bind_param("i", $BuyerID);
                        $stmt->execute();

                        echo "<li>";
                        echo "<h3>User deleted successfully</h3>";
                        echo "<p>User ID: " . htmlspecialchars($userID) . " is no longer registered</p>";
                        echo "<p>If this was a mistake please create a new account!</p>";
                        echo "<p>All data has been removed.</p>";
                        echo "</li>";
                    }
                }
            }
        } else {
            echo "This User ID does not exist or does not have a buyer account type";
        }
    } elseif ($accountType == 'seller') {
        $stmt = $connection->prepare("SELECT SellerID FROM Sellers WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $SellerID = $row['SellerID'];

            $stmt = $connection->prepare("DELETE FROM Notification WHERE UserID = ?");
            $stmt->bind_param("i", $userID);
            if ($stmt->execute()) {
                $stmt = $connection->prepare("DELETE FROM Sellers WHERE UserID = ?");
                $stmt->bind_param("i", $userID);
                if ($stmt->execute()) {
                    $stmt = $connection->prepare("DELETE FROM Users WHERE UserID = ?");
                    $stmt->bind_param("i", $userID);
                    if ($stmt->execute()) {
                        $stmt = $connection->prepare("DELETE FROM AuctionItem WHERE SellerID = ?");
                        $stmt->bind_param("i", $SellerID);
                        $stmt->execute();

                        echo "<li>";
                        echo "<h3>User deleted successfully</h3>";
                        echo "<p>User ID: " . htmlspecialchars($userID) . " is no longer registered</p>";
                        echo "<p>If this was a mistake please create a new account!</p>";
                        echo "<p>All data has been removed.</p>";
                        echo "</li>";
                    }
                }
            }
        } else {
            echo "This User ID does not exist or does not have a seller account type";
        }
    }
} else {
    echo "UserID do not match";
}

?>

  



<?php include_once("footer.php")?>