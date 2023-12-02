<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php
  include 'db_connect.php';

  if (session_id() == '') {
    session_start();
  }

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

    // Fetch images for the auction item
    $imageStmt = $connection->prepare("SELECT ImageID, ImagePath FROM ItemImages WHERE ItemAuctionID = ?");
    $imageStmt->bind_param("i", $itemAuctionID);
    $imageStmt->execute();
    $imagesResult = $imageStmt->get_result();
    $currentImages = $imagesResult->fetch_all(MYSQLI_ASSOC);
    
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
        <form method="post" action="edit_auction_result.php" id="editAuctionForm" enctype="multipart/form-data">
          <input type="hidden" name="itemAuctionID" value="<?php echo htmlspecialchars($itemAuctionID); ?>">

          <div class="form-group row">
            <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="auctionTitle" name="auctionTitle" value="<?php echo htmlspecialchars($auctionTitle); ?>">
              <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span></small>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
            <div class="col-sm-10">
              <textarea class="form-control" id="auctionDetails" name="auctionDetails" rows="4"><?php echo htmlspecialchars($auctionDetails); ?></textarea>
              <small id="detailsHelp" class="form-text text-muted">Full details of the listing to help bidders decide if it's what they're looking for.</small>
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
              <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">£</span>
                    </div>
                    <input type="number" class="form-control" id="auctionStartPrice" name="auctionStartPrice" value="<?php echo htmlspecialchars($auctionStartPrice); ?>" readonly>
                    <small id="startBidHelp" class="form-text text-muted">* Cannot be changed. Please unlist your auction item and create a new auction if you would like to change this.</small>
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
              <small id="reservePriceHelp" class="form-text text-muted">Auctions that end below this price will not go through. This value is not displayed in the auction listing</small>
            </div>
          </div>

          <div class="form-group row">
            <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
            <div class="col-sm-10">
              <input type="datetime-local" class="form-control" id="auctionEndDate" name="auctionEndDate" value="<?php echo htmlspecialchars(str_replace(' ', 'T', $auctionEndDate)); ?>">
              <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
            </div>
          </div>

          <!-- Section for current images -->
          <div class='current-images'>
            <p>Current Images:</p>
            <?php foreach ($currentImages as $image): ?>
              <div class='image-container'>
                <img src='<?php echo htmlspecialchars($image['ImagePath']); ?>' alt='Item Image' style='width: 150px; height: auto;' />
                <label>
                  <input type='checkbox' name='deleteImages[]' value='<?php echo $image['ImageID']; ?>'> Delete this image
                </label>
              </div>
            <?php endforeach; ?>
          </div>
          
          <!-- Image upload input -->
          <div class="form-group row">
            <label for="auctionImages" class="col-sm-2 col-form-label text-right">Add Images</label>
            <div class="col-sm-10">
              <input type="file" class="form-control-file" id="auctionImages" name="auctionImages[]" multiple>
              <small class="form-text text-muted">Upload new images for the item. Existing images marked for deletion will be removed.</small>
            </div>
          </div>

          <button type="submit" class="btn btn-primary form-control">Update Auction</button>
          <button type="button" class="btn btn-danger form-control mt-2" onclick="confirmDeletion(<?php echo $itemAuctionID; ?>)">Delete Auction</button>
          
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              document.getElementById('editAuctionForm').addEventListener('submit', function(e) {
                var titleInput = document.getElementById("auctionTitle").value.trim();
                var categorySelect = document.getElementById("auctionCategory");
                var selectedCategory = categorySelect.options[categorySelect.selectedIndex].value;
                var startDateInput = parseFloat(document.getElementById("auctionStartPrice").value);
                var endDateInput = document.getElementById('auctionEndDate');
                var selectedDate = new Date(endDateInput.value);
                var now = new Date();

                // Title validation
                if (!titleInput) {
                    alert("Please enter a title for the auction.");
                    e.preventDefault();
                    return;
                }

                // Category validation
                if (!selectedCategory || selectedCategory === "Choose...") {
                    alert("Please select a category for the auction.");
                    e.preventDefault();
                    return;
                }

                // Starting price validation
                if (isNaN(startDateInput) || startDateInput <= 0) {
                    alert("Please enter a valid starting price for the auction.");
                    e.preventDefault();
                    return;
                }

                // Date validation
                if (!endDateInput.value.trim() || selectedDate <= now) {
                    alert("Please select a future date for the auction end.");
                    e.preventDefault();
                    return;
                }
                var startingPrice = parseFloat(document.getElementById("auctionStartPrice").value);
                var reservePrice = parseFloat(document.getElementById("auctionReservePrice").value);

                // Check if reserve price is not higher than starting price
                if (reservePrice < startingPrice) {
                  e.preventDefault(); // Prevent form submission
                  alert("Reserve price must be higher than the starting price.");
                }
                
                 // Calculate the total number of images after deletion and new uploads
                var existingImageCount = document.querySelectorAll('.current-images .image-container').length;
                var imagesMarkedForDeletion = document.querySelectorAll('.current-images input[type="checkbox"]:checked').length;
                var maxImages = 4;
                var newImageInputs = document.getElementById('auctionImages');
                var totalNewImages = newImageInputs.files.length;
                var totalImagesAfterUpdate = existingImageCount - imagesMarkedForDeletion + totalNewImages;

                if (totalImagesAfterUpdate > maxImages) {
                  e.preventDefault(); // Prevent form submission
                  alert('You can only have a maximum of ' + maxImages + ' images per auction. Please adjust your images accordingly.');
                }

                // Image format validation
                var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
                var imageInputs = document.getElementById('auctionImages');
                
                for (var i = 0; i < imageInputs.files.length; i++) {
                  var file = imageInputs.files[i];
                  if (!allowedExtensions.exec(file.name)) {
                    e.preventDefault();
                    alert('Please only upload image format files (jpg, jpeg, png, gif).');
                    break;
                  }
                }
              });
            });
            function confirmDeletion(itemId) {
              if (confirm("Are you sure you want to delete this auction? This action cannot be undone.")) {
                window.location.href = 'delete_auction.php?item_id=' + itemId;
              }
            }
          </script>

        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once("footer.php")?>
