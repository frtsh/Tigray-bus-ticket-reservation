<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
        if ($conn->query($sql) === TRUE) {
            header('Location: login.php');
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>

    <div style="background-color: rgba(255, 255, 255, 0.8); padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; margin: auto;">
        <h2 style="text-align: center; color: #444; margin-bottom: 20px;">Register</h2>

        <?php if (isset($error)) { echo "<p style='color: red; font-size: 14px; text-align: center;'>$error</p>"; } ?>

        <form method="POST" action="register.php" style="width: 100%;">
            <label for="name" style="display: block; font-weight: bold; margin-bottom: 8px;">Name:</label>
            <input type="text" id="name" name="name" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"><br>

            <label for="email" style="display: block; font-weight: bold; margin-bottom: 8px;">Email:</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"><br>

            <label for="password" style="display: block; font-weight: bold; margin-bottom: 8px;">Password:</label>
            <input type="password" id="password" name="password" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;"><br>

            <button type="submit" style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s;">
                Register
            </button>
        </form>
    </div>

</body>
</html>

<?php include('footer.php'); ?>
