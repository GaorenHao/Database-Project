<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connect.php';


// Now you can use $connection to interact with the database
?>

<div class="container">

<h2 class="my-3">Browse listing</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" id="keyword" name="keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat" name="cat">
          <option selected value="all">All categories</option>
          <option value="fashion">fashion</option>
          <option value="electronics">electronics</option>
          <option value="home">home</option>
          <option value="beauty">beauty</option>
          <option value="outdoor">outdoor</option>
          <option value="art">art</option>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option selected value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="date">Soonest expiry</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Sanitize and validate GET parameters
  $keyword = isset($_GET['keyword']) ? $connection->real_escape_string($_GET['keyword']) : '';
  $category = isset($_GET['cat']) ? $connection->real_escape_string($_GET['cat']) : 'all';
  $ordering = isset($_GET['order_by']) ? $_GET['order_by'] : 'pricelow';
  $curr_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

  // Construct the SQL query based on parameters
  $sql = "SELECT AuctionItem.*, Categories.CategoryName FROM AuctionItem ";
  $sql .= "LEFT JOIN Categories ON AuctionItem.CategoryID = Categories.CategoryID ";

  if (!empty($keyword)) {
      $sql .= "WHERE AuctionItem.Description LIKE '%$keyword%' ";
  }

  if ($category != 'all') {
      $sql .= (!empty($keyword) ? "AND " : "WHERE ") . "Categories.CategoryName = '$category' ";
  }

  if ($ordering == 'pricelow') {
      $sql .= "ORDER BY AuctionItem.StartingPrice ASC ";
  } elseif ($ordering == 'pricehigh') {
      $sql .= "ORDER BY AuctionItem.StartingPrice DESC ";
  } else {
      $sql .= "ORDER BY AuctionItem.EndDate ASC ";
  }

  // Pagination Logic
  $results_per_page = 10;
  $offset = ($curr_page - 1) * $results_per_page;

  // First, fetch the total number of items for pagination
  $totalItemsQuery = "SELECT COUNT(*) as TotalItems FROM AuctionItem";
  $totalItemsResult = $connection->query($totalItemsQuery);
  $totalItemsRow = $totalItemsResult->fetch_assoc();
  $totalItems = $totalItemsRow['TotalItems'];
  $max_page = ceil($totalItems / $results_per_page);
  // Execute the query
  $sql .= " LIMIT $results_per_page OFFSET $offset";
  $result = $connection->query($sql);

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      echo "Title: " . htmlspecialchars($row["Title"]) . "<br>";
      echo "Description: " . htmlspecialchars($row["Description"]) . "<br>";
      echo "Starting Price: " . htmlspecialchars($row["StartingPrice"]) . "<br>";
      echo "End Date: " . htmlspecialchars($row["EndDate"]) . "<br>";
      echo "Category: " . htmlspecialchars($row["CategoryName"]) . "<br><br>";
    }

    // Proceed with the second query only if the first query had results
    $itemSummary_query = "SELECT AuctionItem.*, COUNT(Bid.BidID) as BidCount, MAX(Bid.BidAmount) as MaxBid FROM AuctionItem JOIN Bid ON AuctionItem.ItemAuctionID = Bid.ItemAuctionID GROUP BY AuctionItem.ItemAuctionID";
    $itemSummary_result = $connection->query($itemSummary_query);

    if ($itemSummary_result && $itemSummary_result->num_rows > 0) {
      while ($row = $itemSummary_result->fetch_assoc()) {
        $item_id = $row['ItemAuctionID']; // Fetching the correct columns
        $title = htmlspecialchars($row['Title']);
        $description = htmlspecialchars($row['Description']);
        $current_price = htmlspecialchars($row['MaxBid']);
        $num_bids = htmlspecialchars($row['BidCount']);
        $end_date = new DateTime($row['EndDate']);

        // Display the details for each auction listing
        // Replace this with your actual listing display logic
        echo "Item ID: " . $item_id . "<br>";
        echo "Title: " . $title . "<br>";
        echo "Description: " . $description . "<br>";
        echo "Current Price: " . $current_price . "<br>";
        echo "Number of Bids: " . $num_bids . "<br>";
        echo "End Date: " . $end_date->format('Y-m-d H:i:s') . "<br><br>";
      }
    }

  } else { 
    echo "No listings found.";
  }
?>


</ul>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
    
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
  
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>



<?php include_once("footer.php")?>
