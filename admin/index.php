<?php
session_start();
include('../includes/db.php');

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin_logged_in']) || !isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
?>

<?php include('../includes/header.php'); ?>

<div class="container">
    <h2 style="text-align: center; margin-top: 20px;">Admin Dashboard</h2>
    <div class="dashboard-links" style="display: flex; flex-direction: column; align-items: center; margin-top: 30px;">
        <a href="manage_users.php" style="margin: 10px; font-size: 18px;">Manage Users</a>
        <a href="manage_buses.php" style="margin: 10px; font-size: 18px;">Manage Buses</a>
        <a href="manage_seats.php" style="margin: 10px; font-size: 18px;">Manage Seats</a>
        <a href="manage_bookings.php" style="margin: 10px; font-size: 18px;">Manage Bookings</a>
        <a href="manage_tickets.php" style="margin: 10px; font-size: 18px;">Manage Tickets</a>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
