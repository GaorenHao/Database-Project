<?php
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include 'db_connect.php';

function sendMail($email, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0; // Enable verbose debug output
        $mail->isSMTP(); // Send using SMTP
        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth   = true; // Enable SMTP authentication
        $mail->Username   = 'databaseprojectucl@gmail.com'; // SMTP username
        $mail->Password   = 'eyldcmjhgpalfugi'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port       = 587; // TCP port to connect to

        // Recipients
        $mail->setFrom('from@example.com', 'Mailer');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo); // Log to PHP error log.
    }
}

// Email Query
$emailQuery = "
    SELECT u.Email
    FROM Notification n
    JOIN Users u ON n.UserID = u.UserID
    ORDER BY n.DateTime DESC
    LIMIT 1;
";

$emailStmt = $connection->prepare($emailQuery);
$emailStmt->execute();
$emailResult = $emailStmt->get_result();

if ($emailResult->num_rows > 0) {
    $emailRow = $emailResult->fetch_assoc();
    $userEmail = $emailRow['Email'];
} else {
    echo "No notifications to send.";
    exit;
}

// Item Query with Prepared Statement
$itemQuery = "
    SELECT a.Title, b.BidAmount AS LatestBid
    FROM Bid b
    JOIN AuctionItem a ON b.ItemAuctionID = a.ItemAuctionID
    WHERE b.BidTime = (
        SELECT MAX(BidTime) 
        FROM Bid
    )
";

$itemStmt = $connection->prepare($itemQuery);
$itemStmt->bind_param("s", $userEmail);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

if ($itemResult->num_rows > 0) {
    $itemRow = $itemResult->fetch_assoc();
    $itemTitle = $itemRow['Title'];
    $latestBid = $itemRow['LatestBid'];

    // Send email
    $subject = 'You have been outbid!';
    $body = "Hello, <br><br> You have been outbid on the item <b>$itemTitle</b>. The current highest bid is &pound;$latestBid<br> <br><br> Visit the item page to place a new bid.";
    sendMail($userEmail, $subject, $body);
} else {
    echo "No relevant auction item found for notification.";
}

$connection->close();
?>