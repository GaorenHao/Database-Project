<?php include_once("header.php")?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */
require once 'db_connect.php';

/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt->bind_param("ssssiis", $ItemAuctionID, $SellerID, $CategoryID, $Description, $StartingPrice, $ReservePrice, $EndDate);
    $ItemAuctionID = htmlspecialchars($_POST['Title of auction']);
    $CategoryID = htmlspecialchars($_POST['Category']);
    $Description = htmlspecialchars($_POST['Details']);
    $StartingPrice = htmlspecialchars($_POST['Starting price']);
    $ReservePrice = htmlspecialchars($_POST['Reserve price']);
    $EndDate = htmlspecialchars($_POST['End date']);

$sql = "INSERT INTO AuctionItem (ItemAuctionID, SellerID, CategoryID, Description, StartingPrice, ReservePrice, EndDate) VALUES (?, ?, ?, ?, ?, ?, ?) "
$stmt = $conn->prepare($sql);
$stmt->blind_param("ssssiis", $ItemAuctionID, $SellerID, $CategoryID, $Description, $StartingPrice, $ReservePrice, $EndDate);

/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
            
            if ($stmt->execute()) {
                echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');
            } else {
                echo "Error occurred: " . $stmt->error;
            }

            $stmt->close();

// If all is successful, let user know.

?>

</div>


<?php include_once("footer.php")?>