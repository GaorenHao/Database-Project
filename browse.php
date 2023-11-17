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
          <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat">
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
        <select class="form-control" id="order_by">
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
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    // TODO: Define behavior if a keyword has not been specified.
    echo "No keyword specified";
  }else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    echo "No category specified";
    // TODO: Define behavior if a category has not been specified
  }
  else {
    $category = $_GET['cat'];
  }
  ////////// can remove this probs!! 
  $sql = "SELECT * FROM AuctionItem ORDER BY StartingPrice";
  $result = $connection->query($sql);

  if ($result->num_rows>0){
    while($row = $result->fetch_assoc()){
      //echo "CategoryID:".$row["CategoryID"]. "- Description:". $row["Description"]." ". $row["StartingPrice"]. "<br>";
    }
  } else {
    echo "0 results";
  }

  // Initialize orderbysql with a default value
  $orderbysql = 'ORDER BY StartingPrice ASC';

  if (!isset($_GET['order_by'])) {
    echo "No order by defined";


  }

    
    // TODO: Define behavior if an order_by value has not been specified.
  else {
    $ordering = $_GET['order_by'];

    if ($ordering == 'pricelow') {
      $orderbysql = 'ORDER BY Starting Price ASC';
    }
    else if ($ordering == 'pricehigh') {
      $orderbysql = 'ORDER BY StartingPrice DESC';
    } else{
      $orderbysql = 'ORDER BY EndDate DESC';
    }
  }


  
  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  /* TODO: Use above values to construct a query. Use this query to 
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */

  $sql = "SELECT * FROM AuctionItem $orderbysql";
  $result = $connection -> query($sql);

  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()){
      echo"Title". $row["Title"] ."Description". $row["Description"]. "StartingPrice" .$row["StartingPrice"]. "EndDate". $row["EndDate"]. "<br>";
    }
  } else { echo "No results found";
  }
    




  $sql = "SELECT * FROM AuctionItem ORDER BY StartingPrice";
  $result = $connection->query($sql);
   
  if ($result->num_rows>0){
    while($row = $result->fetch_assoc()){
  echo "CategoryID:".$row["CategoryID"]. "- Description:". $row["Description"]." ". $row["StartingPrice"]. "<br>";
  }
  } else {
      echo "0 results";
  }

  

  
    




  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  $num_results = 96; // TODO: Calculate me for real
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php
  // Replace the demonstration part with actual fetching and displaying
  // You will replace the SQL query string with your actual SQL query
  
  //$sql = "SELECT ItemAuctionID, SellerID, CategoryID, Description, StartingPrice, ReservePrice, EndDate FROM AuctionItem";
  $itemSummary_query = "SELECT AuctionItem.*, COUNT(Bid.BidID) as BidCount, MAX(Bid.BidAmount) as MaxBid FROM AuctionItem JOIN Bid ON AuctionItem.ItemAuctionID = Bid.ItemAuctionID GROUP BY AuctionItem.ItemAuctionID";
  $result = $connection->query($itemSummary_query);

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $item_id = $row['ItemAuctionID']; // Fetching the correct columns
      $title = $row['Title'];
      $description = $row['Description'];
      $current_price = $row['MaxBid'];
      $num_bids = $row['BidCount'];
      $end_date = new DateTime($row['EndDate']);
      
      print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
    }
  } else {
    echo "<p>No listings found.</p>";
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


