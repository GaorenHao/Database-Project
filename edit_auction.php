<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php
  include 'db_connect.php';

  // Start the session
  session_start();

  // Ensure the user is logged in and is a seller
  if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
    exit;
  }

  // Get the auction item ID from the URL parameter
  $itemAuctionID = isset($_GET['item_id']) ? intval($_GET['item_id']) : null;

  if ($itemAuctionID === null) {
    echo "No auction item specified.";
    exit;
  }

  // Fetch the auction item details from the database
  $stmt = $connection->prepare("SELECT * FROM AuctionItem WHERE ItemAuctionID = ?");
  $stmt->bind_param("i", $itemAuctionID);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $auctionTitle = $row['Title'];
    $auctionDetails = $row['Description'];
    $auctionCategoryID = $row['CategoryID'];
    $auctionStartPrice = $row['StartingPrice'];
    $auctionReservePrice = $row['ReservePrice'];
    $auctionEndDate = $row['EndDate'];
  } else {
    echo "Auction item not found.";
    exit;
  }

  // Fetch categories for the dropdown
  $categoryQuery = "SELECT * FROM Categories";
  $categoryResult = $connection->query($categoryQuery);
  $categories = $categoryResult->fetch_all(MYSQLI_ASSOC);
?>

<div class="container">
  <div style="max-width: 800px; margin: 10px auto">
    <h2 class="my-3">Edit Auction</h2>
    <div class="card">
      <div class="card-body">
        <form method="post" action="edit_auction_result.php" id="editAuctionForm">
          <input type="hidden" name="itemAuctionID" value="<?php echo htmlspecialchars($itemAuctionID); ?>">

          <div class="form-group row">
            <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" value="<?php echo htmlspecialchars($auctionTitle); ?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="auctionDetails" name="auctionDetails" rows="4"><?php echo htmlspecialchars($auctionDetails); ?></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
            <div class="col-sm-10">
              <select class="form-control" id="auctionCategory" name="auctionCategory">
                <?php
                foreach ($categories as $category) {
                  $selected = ($category['CategoryID'] == $auctionCategoryID) ? 'selected' : '';
                  echo "<option value='" . $category['CategoryID'] . "' $selected>" . htmlspecialchars($category['CategoryName']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>

          
          <div class="form-group row">
            <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice" value="<?php echo htmlspecialchars($auctionStartPrice); ?>">
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" class="form-control" id="auctionReservePrice" name="auctionReservePrice" value="<?php echo htmlspecialchars($auctionReservePrice); ?>">
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
            <div class="col-sm-10">
              <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate" value="<?php echo htmlspecialchars(str_replace(' ', 'T', $auctionEndDate)); ?>">
            </div>
          </div>

          <button type="submit" class="btn btn-primary form-control">Update Auction</button>
          
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              document.getElementById('createAuctionForm').addEventListener('submit', function(e) {
                var endDateInput = document.getElementById('auctionEndDate');
                var selectedDate = new Date(endDateInput.value);
                var now = new Date();

                if (selectedDate <= now) {
                  e.preventDefault(); // Prevent form submission
                  alert('Please select a future date for the auction end.');
                }
                var startingPrice = parseFloat(document.getElementById("auctionStartPrice").value);
                var reservePrice = parseFloat(document.getElementById("auctionReservePrice").value);

                // Check if reserve price is not higher than starting price
                if (reservePrice <= startingPrice) {
                  e.preventDefault(); // Prevent form submission
                  alert("Reserve price must be higher than the starting price.");
                }
              });
            });
          </script>

        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once("footer.php")?>
