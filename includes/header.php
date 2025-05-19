<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div style="background-color: #007bff; padding: 10px; color: white;">
        <h2 style="margin: 0; display: inline;">Admin Panel</h2>
        <a href="logout.php" style="float: right; color: white; text-decoration: none;">Logout</a>
    </div>
    <div style="padding: 20px;">
