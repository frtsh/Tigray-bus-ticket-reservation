<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Reservation</title>
    <link rel="stylesheet" href="styles.css">  <!-- Link to the external CSS -->
</head>
<style>
    header {
    background-color: rgba(47, 61, 172, 0.9); /* Tomato red with opacity */
    color: white;
    padding: 15px 0;
    box-shadow: 0 4px 8px rgba(59, 6, 89, 0.4);
    position: sticky;
    top: 0;
    z-index: 1000;
}
</style>
<body>

    <header>
        <nav >
            <ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a  href="home.php">Home</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

</body>
</html>
