<?php
session_start();
include('../includes/db.php');

$error = '';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<?php include('../includes/header.php'); ?>

<h2 style="text-align:center;">Admin Login</h2>

<?php if ($error): ?>
    <p style="color:red; text-align:center;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="login.php" style="max-width:400px; margin:auto;">
    <label>Email:</label>
    <input type="email" name="email" required style="width:100%; padding:8px; margin:10px 0;"><br>

    <label>Password:</label>
    <input type="password" name="password" required style="width:100%; padding:8px; margin:10px 0;"><br>

    <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white;">Login</button>
</form>

<?php include('../includes/footer.php'); ?>
