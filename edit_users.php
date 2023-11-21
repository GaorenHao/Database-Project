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



</div>

<?php
$userID = $_POST['userID']; 
$userIDConfirmation= $_POST['userIDConfirmation']; 

echo "UserID: $userID, Confirmation: $userIDConfirmation";

if ($userID != $userIDConfirmation) {
    echo'User ID do not match';
}else{

    $stmt = $connection->prepare("DELETE FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    
      // Execute the prepared statement
      if ($stmt->execute()) {
        echo "User deleted.";
      } else {
        echo "Error: " . $stmt->error;
      }


}




?>

<?php include_once("footer.php")?>