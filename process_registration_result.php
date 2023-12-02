<?php include_once("header.php")?>

<div class="container my-5">
    <h2>Registration successful, redirecting... please log in</h2>
    <script>
        setTimeout(function(){
            window.location.href = 'browse.php';
        }, 5000); // Redirect after 5 seconds
    </script>
</div>

<?php include_once("footer.php")?>