<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db_connect.php';?>

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
          <option value="fashion">Fashion</option>
          <option value="electronics">Electronics</option>
          <option value="beauty">Beauty</option>
          <option value="home">Home</option>
          <option value="outdoor">Outdoor</option>
          <option value="art">Art</option>
          <option value="books">Books</option>
          <option value="toys">Toys</option>
          <option value="sports">Sports</option>
          <option value="music">Music</option>
          <option value="clothing">Clothing</option>
          <option value="furniture">Furniture</option>
          <option value="technology">Technology</option>
          <option value="automotive">Automotive</option>
          <option value="gardening">Gardening</option>
          <option value="stationery">Stationery</option>
          <option value="pets">Pets</option>
          <option value="healthcare">Healthcare</option>
          <option value="footwear">Footwear</option>
          <option value="jewelry">Jewelry</option>
          <option value="cosmetics">Cosmetics</option>
          <option value="groceries">Groceries</option>
          <option value="beverages">Beverages</option>
          <option value="cookware">Cookware</option>
          <option value="bedding">Bedding</option>
          <option value="decor">Decor</option>
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

<?php
  // Sanitize and validate GET parameters
  $keyword = isset($_GET['keyword']) ? $connection->real_escape_string($_GET['keyword']) : '';
  $category = isset($_GET['cat']) ? $connection->real_escape_string($_GET['cat']) : 'all';
  $ordering = isset($_GET['order_by']) ? $_GET['order_by'] : 'pricelow';
  $curr_page = isset($_GET['page']) ? intval($_GET['page']) : 1;

  // Construct the SQL query to fetch auction items and bid information
  $sql = "SELECT AuctionItem.*, Categories.CategoryName, COUNT(Bid.BidID) AS BidCount, MAX(Bid.BidAmount) AS MaxBid ";
  $sql .= "FROM AuctionItem ";
  $sql .= "LEFT JOIN Categories ON AuctionItem.CategoryID = Categories.CategoryID ";
  $sql .= "LEFT JOIN Bid ON AuctionItem.ItemAuctionID = Bid.ItemAuctionID ";

  // Add conditions based on category and keyword
  if ($category != 'all') {
      $sql .= "WHERE Categories.CategoryName = '$category' ";
      if (!empty($keyword)) {
          $sql .= "AND (AuctionItem.Description LIKE '%$keyword%' OR AuctionItem.Title LIKE '%$keyword%') ";
      }
  } elseif (!empty($keyword)) {
      $sql .= "WHERE (AuctionItem.Description LIKE '%$keyword%' OR AuctionItem.Title LIKE '%$keyword%') ";
  }

  // Group by ItemAuctionID to aggregate bid data
  $sql .= "GROUP BY AuctionItem.ItemAuctionID ";

// Apply the Order By condition last
if ($ordering == 'pricelow') {
    $sql .= "ORDER BY AuctionItem.StartingPrice ASC ";
} elseif ($ordering == 'pricehigh') {
    $sql .= "ORDER BY AuctionItem.StartingPrice DESC ";
} else {
    $sql .= "ORDER BY AuctionItem.EndDate ASC ";
}

  // Pagination Logic
  $results_per_page = 12;
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
      $itemCount = 0;
      echo '<div class="row">'; // Start the first row

      while ($row = $result->fetch_assoc()) {
          // Check if we need to end the current row and start a new one
          if ($itemCount > 0 && $itemCount % 4 == 0) {
              echo '</div><div class="row">';
          }
          $itemLink = "listing.php?item_id=" . urlencode($row['ItemAuctionID']);
          // Display the details for each auction listing in a column
          
          echo '<div class="col-md-3">';
          echo '<a href="' . $itemLink . '" class="item-link">';
          echo '<div class="item-box">';
          echo "<h5>" . htmlspecialchars($row['Title']) . "</h5>";
          // Truncate the description to a specific character length for a non-CSS solution
          $maxLength = 100; 
          $description = $row['Description'];
          $shortDescription = (strlen($description) > $maxLength) ? substr($description, 0, $maxLength) . "..." : $description;

          echo "<p class='description'>" . htmlspecialchars($shortDescription) . "</p>";
          
          echo '<div class="item-info">';
          echo "  <p>Starting Price: £" . htmlspecialchars($row['StartingPrice']) . "</p>";
          echo "  <p>End Date: " . htmlspecialchars($row['EndDate']) . "</p>";
          echo "  <p>Category: " . htmlspecialchars($row['CategoryName']) . "</p>";
          echo '</div>'; 
          echo '</div>';
          echo '</div>';
          $itemCount++; // Increment the item count
      }

      echo '</div>'; // Close the last row div
  } else {
      echo "No listings found.";
  }
?>


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
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Parse the URL parameters
        const params = new URLSearchParams(window.location.search);
        
        // Get parameters for category and order_by
        const category = params.get('cat');
        const orderBy = params.get('order_by');
        const keyword = params.get('keyword'); 

        // Set the dropdowns to reflect the current parameters
        if (category) {
            document.getElementById('cat').value = category;
        }
        if (orderBy) {
            document.getElementById('order_by').value = orderBy;
        }
        if (keyword) {
            document.getElementById('keyword').value = keyword;
        }
    });
</script>

<?php include_once("footer.php")?>