<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check user credentials
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: home.php');
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body style="    margin: 0;
    font-family: Arial, sans-serif;
    background-image: url('image.jpg');
    background-size: cover;">

    <div style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; margin: auto;">
        <h2 style="text-align: center; color: #444; margin-bottom: 20px;">Login</h2>
        
        <?php if (isset($error)) { echo "<p style='color: red; font-size: 14px; text-align: center;'>$error</p>"; } ?>
        
        <form method="POST" action="login.php" style="width: 100%;">
            <label for="email" style="display: block; font-weight: bold; margin-bottom: 8px;">Email:</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"><br>

            <label for="password" style="display: block; font-weight: bold; margin-bottom: 8px;">Password:</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"><br>

            <button type="submit" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
                Login
            </button>
        </form>
    </div>

</body>
</html>

<?php include('footer.php'); ?>
