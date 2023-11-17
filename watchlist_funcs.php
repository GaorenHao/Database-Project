 <?php

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = $_POST['arguments'];

if ($_POST['functionname'] == "add_to_watchlist") {
  $stmt = $connection->prepare("INSERT INTO Watchlist (Item, email, password, FirstName, LastName) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $accountType, $email, $password, $firstName, $lastName);
  // TODO: Update database and return success/failure.


  $res = "success";
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.


  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>

$stmt = $connection -> prepare("INSERT SellerID FROM Sellers WHERE UserID = ?");