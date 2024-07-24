<?php
session_start();

// Check if the user is logged in
if(!isset($_SESSION['username'])) {
    // Redirect to login page or show an error message
    header("Location: login.php");
    exit;
}

// Logout logic
if(isset($_POST['logout'])) {
    // Destroy the session and redirect to landing page
    session_destroy();
    header("Location: index.php"); // Assuming index.php is your landing page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
</head>
<body>

    <!-- Logout Form -->
    <form action="" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

</body>
</html>
