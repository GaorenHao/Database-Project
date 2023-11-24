<?php
  // FIXME: At the moment, I've allowed these values to be set manually.
  // But eventually, with a database, these should be set automatically
  // ONLY after the user's login credentials have been verified via a 
  // database query.
  session_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  include 'db_connect.php';
  include 'win_funcs.php';

  /// the below is all creation of the notificaton pop up... 
  // Get the current user's ID
  // Assuming the user ID is stored in the session

  if (isset($_SESSION['username'])) {
    $userId = $_SESSION['username']; 
  

  check_ending_listings($connection, $userId);

  // Create the DateTime object
  $now = new DateTime();
  // Format the DateTime object to a string
  $formattedNow = $now->format('Y-m-d H:i:s');
  // Prepare the query
  $notifQuery = "SELECT Message, NotificationID FROM Notification WHERE UserID = '$userId' AND DateTime < NOW() AND `Read` = 0";
  // Execute the query
  $result = $connection->query($notifQuery);
  // Check for a matching notification
  if ($result->num_rows > 0) {
      // Fetch the notification data
      $notification = $result->fetch_assoc();
      // Pass the notification to JavaScript
      echo "<script>";
    echo "var notificationMessage = " . json_encode($notification['Message']) . ";";
    echo "var markNotifRead = " . json_encode($notification['NotificationID']) . ";"; 
    echo "</script>";
    }

  // Check if this is an AJAX request for deleting a notification
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['markNotifRead'])) {
    $deleteNotifQuery = $connection->prepare("UPDATE Notification SET `Read` = 1 WHERE NotificationID = ?");
    $deleteNotifQuery->bind_param("i", $_POST['markNotifRead']); 
    $deleteNotifQuery->execute();

    if ($deleteNotifQuery->affected_rows > 0) {
        echo "Notification marked as read successfully";
    } else {
        echo "Error or no notif found to mark as read";
    }
  }
} else {
  echo "Not logged in";
}
?>



<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">

  <script>
        // Check if the notificationMessage variable is set
        if (typeof notificationMessage !== 'undefined' && typeof markNotifRead !== 'undefined') { //////// change undefined to null maybe ?
          window.onload = function() {
              alert("Notification: " + notificationMessage);
              sendDismissNotification(markNotifRead); // Pass the notification ID to the function
          };
        }

        function sendDismissNotification(markNotifRead) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Same file
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    console.log(this.responseText); // Log server response
                }
            }

            xhr.send("markNotifRead=" + markNotifRead); ////// where is this being sent to 
        }
        
    </script>

  <title>Awesome Auction</title>
</head>


<body>

<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
  <a class="navbar-brand" href="browse.php">Awesome Auctions<!--CHANGEME!--></a>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
    
<?php
  // Displays either login or logout on the right, depending on user's
  // current status (session).
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    echo '<a class="nav-link" href="logout.php">Logout</a>';
  }
  else {
    echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
  }
?>

    </li>
  </ul>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <ul class="navbar-nav align-middle">
	<li class="nav-item mx-1">
      <a class="nav-link" href="browse.php">Browse</a>
    </li>
<?php
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
  echo('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mybids.php">My Bids</a>
    </li>
	<li class="nav-item mx-1">
      <a class="nav-link" href="recommendations.php">Recommended</a>
    </li>
    <li class="nav-item mx-1">
      <a class="nav-link" href="watchlist.php">My Watchlist</a>
    </li>');
  }
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'seller') {
  echo('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mylistings.php">My Listings</a>
    </li>
	<li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
    </li>');
  }
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'admin') {
    echo('
    <li class="nav-item mx-1">
        <a class="nav-link" href="admin.php">View Users</a>
      </li>
    <li class="nav-item ml-3">
    <a class="nav-link btn border-light" href="admin.php">- Manage Users</a>
    </li>');
    
   
  }
?>
  </ul>
</nav>

<!-- Login modal -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form method="POST" action="login_result.php">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" class="form-control" id="email" placeholder="Email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
          </div>
          <button type="submit" class="btn btn-primary form-control">Sign in</button>
        </form>
        <div class="text-center">or <a href="register.php">create an account</a></div>
      </div>

    </div>
  </div>
</div> <!-- End modal -->