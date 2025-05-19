<?php
session_start();
include('../db.php');

$error = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);

    // Hash the password before inserting it into the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the admin into the database
    $sql = "INSERT INTO admin (email, password, name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $hashed_password, $name);
    
    if ($stmt->execute()) {
        // Registration successful
        header("Location: login.php");
        exit();
    } else {
        $error = "Error creating admin account!";
    }
}

?>

<!-- HTML form for admin registration -->
<?php include('header.php'); ?>

<h2 style="text-align:center;">Create Admin Account</h2>

<?php if ($error): ?>
    <p style="color:red; text-align:center;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="admin_register.php" style="max-width:400px; margin:auto;">
    <label>Name:</label>
    <input type="text" name="name" required style="width:100%; padding:8px; margin:10px 0;"><br>

    <label>Email:</label>
    <input type="email" name="email" required style="width:100%; padding:8px; margin:10px 0;"><br>

    <label>Password:</label>
    <input type="password" name="password" required style="width:100%; padding:8px; margin:10px 0;"><br>

    <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white;">Create Account</button>
</form>

<?php include('footer.php'); ?>
