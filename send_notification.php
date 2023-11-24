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
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
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
        echo 'Message has been sent to ' . $email . "\n";
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo); // Log to PHP error log.
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
    }
}

// Adjust this query based on your application's logic
$query = "
        SELECT u.Email
        FROM Notification n
        JOIN Users u ON n.UserID = u.UserID
        ORDER BY n.DateTime DESC
        LIMIT 1;
";

$result = $connection->query($query);

if (!$result) {
    echo "Error: " . $connection->error;
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['Email'];
        $subject = 'You have been outbid!';
        $body = 'Hello, <br><br> You have been outbid on the item <b>' . $row['Title'] . '</b>. The current highest bid is £' . $row['LatestBid'] . '.<br><br> Visit the item page to place a new bid.';

        sendMail($email, $subject, $body);
    }
} else {
    echo "No notifications to send.";
}

$connection->close();
?>
