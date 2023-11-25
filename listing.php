<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  include 'db_connect.php';

  // Get item_id from the URL:
  $item_id = $_GET['item_id'];

  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  $has_session = true;
  $watching = false; 

  

  if (!empty($_SESSION)) {
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    $has_session = true;
    // the watchlist is only relevant to buyers only, so only if account type is buyer, 
    // start executing the search inside watchlist 
    if ($_SESSION['account_type'] == 'buyer') {
      $buyerID = $_SESSION['buyerid'];
      // check if current listing item is being watched by the current buyer... 
      $searchWatchlist_query = $connection->prepare("SELECT * FROM WatchListItems WHERE BuyerID = ? AND ItemAuctionID = ?");
      $searchWatchlist_query->bind_param("ii", $buyerID, $item_id); // 'ii' specifies that both parameters are integers
      $searchWatchlist_query->execute();
      $searchWatchlist_query->store_result();
      // Check if the watchlist contains the buyer id and item id pairing
      if ($searchWatchlist_query->num_rows > 0) {
          // Pairing exists
          $watching = true;
      } else {
          // Pairing does not exist
          $watching = false;
    }
    }
  } else {
    echo "No session variables are set.";
    $has_session = false;
    $watching = false;
  }

  // TODO: Use item_id to make a query to the database.
  $sql = "SELECT * FROM AuctionItem WHERE ItemAuctionID = $item_id";
  $result = $connection->query($sql);
  
  if ($result->num_rows > 0) {
    // Fetching the first row of the result
    $row = $result->fetch_assoc();

    $title = $row['Title'];
    $description = $row['Description'];
    $reservePrice = $row['ReservePrice'];
    $num_bids = 1; //// hard coded, need to change 
    $end_time = new DateTime($row['EndDate']);

    ////  <!-- BID LOGIC >>>> NEW BIDS MUST BE HIGHER THAN THE CURRENT HIGHEST BID -->

    ///////// #1 get the highest bid from the database
    // Assuming $item_id is the ID of the item being bid on
    // $highest_bid_query = "SELECT MAX(BidAmount) AS highestBid FROM Bid WHERE ItemAuctionID = $item_id";
    $highest_bid_query = "SELECT BuyerID, BidAmount FROM Bid WHERE BidAmount = (SELECT MAX(BidAmount) FROM Bid WHERE ItemAuctionID = $item_id) AND ItemAuctionID = $item_id";

    // Now, find the buyer id that made the highest big 
    $result2 = $connection->query($highest_bid_query);

    if ($result2) {
        $row2 = $result2->fetch_assoc();
        $current_price = $row2['BidAmount'] ?? $row['StartingPrice']; // Default to starting price if no bid exists
        $highest_buyerid = $row2['BuyerID'];
    } else {
        // Handle query error
        echo "Error: " . $connection->error;
    }

    
} else {
    echo "No results found.";
}

  // DELETEME: For now, using placeholder data.
  $num_bids = 1;

  // TODO: Note: Auctions that have ended may pull a different set of data,
  //       like whether the auction ended in a sale or was cancelled due
  //       to lack of high-enough bids. Or maybe not.
  
  // Calculate time to auction end:
  $now = new DateTime();
  
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }


?>



<div class="container">
<!-- Row #1 with auction title + watch button -->
<div class="row"> 
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
  </div>
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php
  /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
  if ($now < $end_time):
?>
    <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist(this)" data-item-id="<?php echo $item_id; ?>">+ Add to watchlist</button>
    </div>
    <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist(this)" data-item-id="<?php echo $item_id; ?>">Remove watch</button>
    </div>
<?php endif /* Print nothing otherwise */ ?>
  </div>
</div>

<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="itemDescription">
    <?php echo($description); ?>
    </div>

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

    <p>
<?php if ($now > $end_time): ?>
     This auction ended <?php echo(date_format($end_time, 'j M H:i')) ?>

    <!-- Compare current price with reserve price, and declare the winning bid / buyer -->
    <?php if ($current_price >= $reservePrice): ?>
      <p>The winning buyer is buyerID <?php echo $highest_buyerid ?>. Congrats!!</p>
    <?php else: ?>
      <p>The reserve bid has not been met. Item not sold.</p>
    <?php endif ?>
     <!-- TODO: Print the result of the auction here? -->
<?php else: ?>
     Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>  
    <p class="lead">Current bid: £<?php echo(number_format($current_price, 2)) ?></p>

    <!-- Bidding form -->
    <form method="POST" action="place_bid.php">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">£</span>
        </div>
	    <input type="number" name="bid" class="form-control" id="bid">
      
      <?php 

      ////  #1 get the highest bid from the database - already done in the above, but send it to the place_bid.php file... 
      ?>
      <!-- Hidden field for id -->
      <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
      <input type="hidden" name="current_price" value="<?php echo htmlspecialchars($current_price); ?>">
      <input type="hidden" name="previous_highest_buyerid" value="<?php echo htmlspecialchars($highest_buyerid); ?>">
      </div>
      <button type="submit" class="btn btn-primary form-control">Place bid</button>
    </form>
    <!-- TABLE OF ALL BIDS -->
    <table>
          <thead>
              <tr>
                  <th>User</th>
                  <th>Bid</th>
                  <th>Time</th>
              </tr>
          </thead>
          <tbody>
          <?php
                $sql = "SELECT BuyerID, BidAmount, BidTime FROM Bid WHERE ItemAuctionID = $item_id";
                $result2 = $connection->query($sql);
                if ($result2->num_rows > 0) {
                  // Output data of each row
                  while($row2 = $result2->fetch_assoc()) {
                      echo "<tr><td>" . $row2["BuyerID"]. "</td><td>" . $row2["BidAmount"] . "</td><td>" . $row2["BidTime"] . "</td></tr>";
                      
                  }
              } else {
                  echo "No bids yet";
              }
            ?>
          </tbody>
          
      </table>
      <p>The highest bidder is currently buyerID <?php echo $highest_buyerid ?></p>
<?php endif ?>

  
  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->



<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

function addToWatchlist(button) {

  var itemId = button.getAttribute('data-item-id'); // Retrieve item_id from data attribute
  console.log(itemId)

  // AJAX call to send item_id to the server
  $.ajax({
    url: 'watchlist_funcs.php', // The PHP file where you handle the item_id
    type: 'POST',
    data: { item_id: itemId, functionname: 'add_to_watchlist',}, // Send item_id as POST data
    success: function(data) {
      console.log('added to watchlist')
      var trimmedData = data.trim();
      if (trimmedData === "success") {
        $("#watch_nowatch").hide();
        $("#watch_watching").show();
      } else {
        var mydiv = document.getElementById("watch_nowatch");
        mydiv.appendChild(document.createElement("br"));
        mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
      }
    },

    error: function(error) {
      // Handle error
      console.error('Error adding item to watchlist', error);
    }
  });



  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
/*   $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},
    /////////// sending data via post to watchlist_funcs.php

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call */

} // End of addToWatchlist func


function removeFromWatchlist(button) {

var itemId = button.getAttribute('data-item-id'); // Retrieve item_id from data attribute
console.log(itemId)

// AJAX call to send item_id to the server
$.ajax({
  url: 'watchlist_funcs.php', // The PHP file where you handle the item_id
  type: 'POST',
  data: { item_id: itemId, functionname: 'remove_from_watchlist',}, // Send item_id as POST data
  success: function(data) {
      console.log('removed from watchlist')
      var trimmedData = data.trim();
      if (trimmedData === "success") {
        $("#watch_watching").hide();
        $("#watch_nowatch").show();
      } else {
        var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
      }
    },
  error: function(error) {
    console.error('Error removing item from watchlist', error);
  }
});

} 


/* function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
        }
        else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func */
</script>