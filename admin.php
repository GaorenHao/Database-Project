<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Registered Accounts</h2>

<?php


include 'db_connect.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 
  
 

   if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'admin') {

    $stmt = $connection->prepare('SELECT * FROM Users');

    if ($stmt === false) {
       
        die("Error: " . $connection->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li>";
            echo "<h3>" . htmlspecialchars($row['Email']) . "</h3>";
            echo "<p>User ID: " . htmlspecialchars($row['UserID']) . "</p>";
            echo "<p>Role: " . htmlspecialchars($row['Role']) . "</p>";
            echo "</li>";
        }
    } else { 
        echo "No users found.";
    }   

   
    

} else {
    echo "Access denied. Only admin can view this page.";
}


?>
</div>


