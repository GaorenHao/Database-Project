<?php include_once("header.php")?>

<?php
//(Uncomment this block to redirect people without selling privileges away from this page)
  // If user is not logged in or not a seller, they should not be able to
  // use this page.
if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
  }

// Fetch categories for the dropdown
$categoryQuery = "SELECT * FROM Categories";
$categoryResult = $connection->query($categoryQuery);
$categories = $categoryResult->fetch_all(MYSQLI_ASSOC);
?>

<div class="container">

  <!-- Create auction form -->
  <div style="max-width: 800px; margin: 10px auto">
    <h2 class="my-3">Create new auction</h2>
    <div class="card">
      <div class="card-body">
        <form method="post" action="create_auction_result.php" id="createAuctionForm" enctype="multipart/form-data">
          <div class="form-group row">
            <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" placeholder="e.g. Black mountain bike" maxlength="255">
              <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="auctionDetails" name="auctionDetails" rows="4"></textarea>
              <small id="detailsHelp" class="form-text text-muted">Full details of the listing to help bidders decide if it's what they're looking for.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
            <div class="col-sm-10">
              <select class="form-control" id="auctionCategory" name="auctionCategory">
                  <option selected>Choose...</option>
                  <?php foreach ($categories as $category): ?>
                      <option value="<?php echo htmlspecialchars($category['CategoryID']); ?>">
                          <?php echo htmlspecialchars($category['CategoryName']); ?>
                      </option>
                  <?php endforeach; ?>
              </select>
              <small id="categoryHelp" class="form-text text-muted">
                  <span class="text-danger">* Required.</span> Select a category for this item.
              </small>
          </div>
          </div>
          <div class="form-group row">
            <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
            <div class="col-sm-10">
            <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice">
              </div>
              <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
            <div class="col-sm-10">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">£</span>
                </div>
                <input type="number" class="form-control" id="auctionReservePrice" name="auctionReservePrice">
              </div>
              <small id="reservePriceHelp" class="form-text text-muted">Auctions that end below this price will not go through. This value is not displayed in the auction listing. If not specified, it will be automatically set to your starting price.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
            <div class="col-sm-10">
              <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate">
              <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
            </div>
          </div>
          <div class="form-group row">
            <label for="auctionImages" class="col-sm-2 col-form-label text-right">Item Images</label>
            <div class="col-sm-10">
              <input type="file" class="form-control-file" id="auctionImages" name="auctionImages[]" multiple>
              <small class="form-text text-muted">Upload up to 4 images for the item.</small>
            </div>
          </div>
          <button type="submit" class="btn btn-primary form-control">Create Auction</button>
          <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('createAuctionForm').addEventListener('submit', function(e) {

                    // Fetching form field values
                    var auctionTitle = document.getElementById("auctionTitle").value;
                    var auctionCategory = document.getElementById("auctionCategory").value;
                    var auctionStartPrice = document.getElementById("auctionStartPrice").value;
                    var auctionEndDate = document.getElementById("auctionEndDate").value;

                    // Validation for required fields
                    if (!auctionTitle.trim()) {
                        alert("Please enter a title for the auction.");
                        e.preventDefault();
                        return;
                    }
                    if (auctionCategory === "Choose..." || !auctionCategory.trim()) {
                        alert("Please select a category.");
                        e.preventDefault();
                        return;
                    }
                    if (!auctionStartPrice.trim()) {
                        alert("Please enter a starting price.");
                        e.preventDefault();
                        return;
                    }

                    // Validation for required date & future date
                    var selectedDate = new Date(auctionEndDate);
                    var now = new Date();
                    if (!auctionEndDate.trim() || selectedDate <= now) {
                        alert("Please select a future date for the auction end.");
                        e.preventDefault();
                        return;
                    }

                    // Validation for reserve price
                    var reservePrice = parseFloat(document.getElementById("auctionReservePrice").value);
                    if (reservePrice && reservePrice < parseFloat(auctionStartPrice)) {
                        alert("Reserve price must be higher than the starting price.");
                        e.preventDefault();
                        return;
                    }

                    // Validation for image upload
                    var imageInputs = document.getElementById('auctionImages');
                    var maxImages = 4;
                    if (imageInputs.files.length > maxImages) {
                        alert('You can only upload a maximum of ' + maxImages + ' images.');
                        e.preventDefault();
                        return;
                    }

                    // Validation for image format
                    var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                    for (var i = 0; i < imageInputs.files.length; i++) {
                        var file = imageInputs.files[i];
                        if (!allowedExtensions.exec(file.name)) {
                            alert('Please only upload image format files (jpg, jpeg, png, gif).');
                            e.preventDefault();
                            return;
                        }
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